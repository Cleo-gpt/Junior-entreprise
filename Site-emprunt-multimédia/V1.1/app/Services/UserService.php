<?php

namespace App\Services;

use App\Core\Database;
use DateTimeImmutable;
use PDO;
use function config;

class UserService
{
    private const ALLOWED_STATUSES = ['pending', 'active', 'suspended'];
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Database::connection();
    }

    public function findByEmail(string $email, bool $includePassword = false): ?array
    {
        $stmt = $this->connection->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => strtolower(trim($email))]);

        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        $user['roles'] = $this->getRolesForUser($user['id']);

        if (!$includePassword) {
            unset($user['password']);
        }

        return $user;
    }

    public function create(array $data, array $roles): array
    {
        $this->ensureRolesExist($roles);

        $id = $data['id'] ?? uniqid('user_', true);
        $email = strtolower(trim($data['email']));

        $status = strtolower(trim($data['status'] ?? 'pending'));
        if (!in_array($status, self::ALLOWED_STATUSES, true)) {
            $status = 'pending';
        }

        $stmt = $this->connection->prepare(
            'INSERT INTO users (id, name, email, password, status, created_at)
             VALUES (:id, :name, :email, :password, :status, NOW())'
        );

        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'email' => $email,
            'password' => $data['password'],
            'status' => $status,
        ]);

        $this->assignRoles($id, $roles);

        return $this->findByEmail($email);
    }

    public function findById(string $id, bool $includePassword = false): ?array
    {
        $stmt = $this->connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        $user = $stmt->fetch();
        if (!$user) {
            return null;
        }

        $user['roles'] = $this->getRolesForUser($user['id']);

        if (!$includePassword) {
            unset($user['password']);
        }

        return $user;
    }

    /**
     * @return array<int, array{id:string,name:string,email:string,status:string,created_at:string,roles:array<int,string>}>
     */
    public function all(): array
    {
        $stmt = $this->connection->query(
            'SELECT id, name, email, status, created_at
             FROM users
             ORDER BY created_at DESC'
        );

        $users = $stmt->fetchAll();

        return array_map(function ($user) {
            $user['roles'] = $this->getRolesForUser($user['id']);
            return $user;
        }, $users);
    }

    public function ensureAdminExists(): void
    {
        $this->ensureRolesExist(['admin', 'responsable', 'enseignant', 'etudiant']);

        $stmt = $this->connection->prepare(
            'SELECT COUNT(*) as total
             FROM users u
             JOIN user_roles ur ON ur.user_id = u.id
             JOIN roles r ON r.id = ur.role_id
             WHERE r.name = :role'
        );

        $stmt->execute(['role' => 'admin']);
        $count = (int) $stmt->fetchColumn();

        if ($count > 0) {
            return;
        }

        $password = password_hash('Admin@123', PASSWORD_DEFAULT);

        $this->create(
            [
                'id' => uniqid('user_', true),
                'name' => 'Administrateur',
                'email' => 'admin@eduvaud.ch',
                'password' => $password,
                'status' => 'active',
            ],
            ['admin', 'responsable']
        );
    }

    public function isAllowedEmail(string $email): bool
    {
        $stmt = $this->connection->prepare(
            'SELECT COUNT(*)
             FROM student_whitelist
             WHERE email = :email
               AND (starts_at IS NULL OR starts_at <= CURRENT_DATE())
               AND (ends_at IS NULL OR ends_at >= CURRENT_DATE())'
        );

        $stmt->execute(['email' => strtolower(trim($email))]);

        return ((int) $stmt->fetchColumn()) > 0;
    }

    public function addToWhitelist(string $email, ?string $startsAt = null, ?string $endsAt = null): void
    {
        $email = strtolower(trim($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Adresse email invalide.');
        }

        $allowedDomain = config('auth.allowed_domain', '');
        if ($allowedDomain && !str_ends_with($email, $allowedDomain)) {
            throw new \InvalidArgumentException('L’adresse email doit se terminer par ' . $allowedDomain . '.');
        }

        [$normalizedStart, $normalizedEnd] = $this->normalizeWhitelistPeriod($startsAt, $endsAt);

        $stmt = $this->connection->prepare(
            'INSERT INTO student_whitelist (email, starts_at, ends_at)
             VALUES (:email, :starts_at, :ends_at)
             ON DUPLICATE KEY UPDATE
                starts_at = VALUES(starts_at),
                ends_at = VALUES(ends_at)'
        );

        $stmt->execute([
            'email' => $email,
            'starts_at' => $normalizedStart,
            'ends_at' => $normalizedEnd,
        ]);
    }

    public function removeFromWhitelist(string $email): void
    {
        $stmt = $this->connection->prepare('DELETE FROM student_whitelist WHERE email = :email');
        $stmt->execute(['email' => strtolower(trim($email))]);
    }

    /**
     * @return array<int, array{email:string,starts_at:?string,ends_at:?string,active:bool}>
     */
    public function whitelist(): array
    {
        $stmt = $this->connection->query(
            'SELECT email, starts_at, ends_at
             FROM student_whitelist
             ORDER BY email ASC'
        );

        $rows = $stmt->fetchAll();
        $today = (new DateTimeImmutable('today'))->format('Y-m-d');

        return array_map(function ($row) use ($today) {
            $startsAt = $row['starts_at'] ?? null;
            $endsAt = $row['ends_at'] ?? null;

            $isActive =
                (!$startsAt || $startsAt <= $today) &&
                (!$endsAt || $endsAt >= $today);

            return [
                'email' => $row['email'],
                'starts_at' => $startsAt ?: null,
                'ends_at' => $endsAt ?: null,
                'active' => $isActive,
            ];
        }, $rows);
    }

    /**
     * @return array{processed:int,imported:int,updated:int,skipped:int,errors:array<int,string>}
     */
    public function importWhitelistFromCsv(
        string $filePath,
        ?string $defaultStartsAt = null,
        ?string $defaultEndsAt = null
    ): array {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \InvalidArgumentException('Fichier CSV introuvable ou illisible.');
        }

        [$defaultStart, $defaultEnd] = $this->normalizeWhitelistPeriod($defaultStartsAt, $defaultEndsAt);

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new \RuntimeException('Impossible de lire le fichier CSV.');
        }

        $lines = preg_split("/\r\n|\n|\r/", $content);
        $delimiter = $this->detectCsvDelimiter($lines);

        $processed = 0;
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $existing = $this->currentWhitelistIndex();

        foreach ($lines as $index => $line) {
            if ($line === '' || trim($line) === '') {
                continue;
            }

            $row = str_getcsv($line, $delimiter);
            if (!$row) {
                $skipped++;
                $errors[] = sprintf('Ligne %d: format CSV invalide.', $index + 1);
                continue;
            }

            $cells = array_map(fn ($value) => trim((string) $value), $row);
            if ($index === 0 && isset($cells[0])) {
                $cells[0] = preg_replace('/^\xEF\xBB\xBF/', '', $cells[0]);
            }

            // Header detection
            if ($processed === 0 && isset($cells[0]) && strcasecmp($cells[0], 'email') === 0) {
                continue;
            }

            $processed++;

            $email = strtolower($cells[0] ?? '');
            $startInput = $cells[1] ?? null;
            $endInput = $cells[2] ?? null;

            if ($email === '') {
                $skipped++;
                $errors[] = sprintf('Ligne %d: email manquant.', $index + 1);
                continue;
            }

            try {
                $startValue = ($startInput !== null && $startInput !== '') ? $startInput : $defaultStart;
                $endValue = ($endInput !== null && $endInput !== '') ? $endInput : $defaultEnd;
                $this->addToWhitelist($email, $startValue, $endValue);
            } catch (\Throwable $e) {
                $skipped++;
                $errors[] = sprintf('Ligne %d (%s): %s', $index + 1, $email, $e->getMessage());
                continue;
            }

            if (isset($existing[$email])) {
                $updated++;
            } else {
                $imported++;
            }

            $existing[$email] = true;
        }

        return [
            'processed' => $processed,
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    public function updateRoles(string $userId, array $roles): void
    {
        $this->ensureRolesExist($roles);
        $this->assignRoles($userId, $roles);
    }

    public function updateStatus(string $userId, string $status): void
    {
        $status = strtolower(trim($status));

        if (!in_array($status, self::ALLOWED_STATUSES, true)) {
            throw new \InvalidArgumentException('Statut utilisateur invalide.');
        }

        $stmt = $this->connection->prepare(
            'UPDATE users SET status = :status WHERE id = :id'
        );

        $stmt->execute([
            'status' => $status,
            'id' => $userId,
        ]);
    }

    public function delete(string $userId): void
    {
        $stmt = $this->connection->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $userId]);
    }

    public function availableRoles(): array
    {
        return ['admin', 'responsable', 'enseignant', 'etudiant'];
    }

    public function availableStatuses(): array
    {
        return self::ALLOWED_STATUSES;
    }

    private function getRolesForUser(string $userId): array
    {
        $stmt = $this->connection->prepare(
            'SELECT r.name
             FROM roles r
             JOIN user_roles ur ON ur.role_id = r.id
             WHERE ur.user_id = :user_id'
        );

        $stmt->execute(['user_id' => $userId]);

        return array_map(fn ($row) => $row['name'], $stmt->fetchAll());
    }

    private function assignRoles(string $userId, array $roles): void
    {
        $stmtDelete = $this->connection->prepare('DELETE FROM user_roles WHERE user_id = :user_id');
        $stmtDelete->execute(['user_id' => $userId]);

        $stmtRole = $this->connection->prepare('SELECT id FROM roles WHERE name = :name');

        $stmtInsert = $this->connection->prepare(
            'INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)'
        );

        foreach ($roles as $roleName) {
            $stmtRole->execute(['name' => $roleName]);
            $roleId = $stmtRole->fetchColumn();

            if (!$roleId) {
                continue;
            }

            $stmtInsert->execute([
                'user_id' => $userId,
                'role_id' => $roleId,
            ]);
        }
    }

    private function ensureRolesExist(array $expectedRoles): void
    {
        $expected = array_unique(array_merge(
            ['admin', 'responsable', 'enseignant', 'etudiant'],
            $expectedRoles
        ));

        $stmt = $this->connection->prepare('INSERT IGNORE INTO roles (name) VALUES (:name)');

        foreach ($expected as $role) {
            $stmt->execute(['name' => $role]);
        }
    }

    /**
     * @return array<string, bool>
     */
    private function currentWhitelistIndex(): array
    {
        $stmt = $this->connection->query('SELECT email FROM student_whitelist');
        $index = [];
        foreach ($stmt->fetchAll() as $row) {
            $index[strtolower($row['email'])] = true;
        }

        return $index;
    }

    /**
     * @return array{0:?string,1:?string}
     */
    private function normalizeWhitelistPeriod(
        ?string $startsAt,
        ?string $endsAt,
        bool $defaultStartToToday = false
    ): array {
        $start = $this->parseOptionalDate($startsAt);
        $end = $this->parseOptionalDate($endsAt);

        if ($defaultStartToToday && !$start) {
            $today = (new DateTimeImmutable('today'))->format('Y-m-d');
            $start = $today;
        }

        if ($start && $end && $start > $end) {
            throw new \InvalidArgumentException('La date de fin doit être postérieure ou égale à la date de début.');
        }

        return [$start, $end];
    }

    private function detectCsvDelimiter(array $lines): string
    {
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $commaCount = substr_count($line, ',');
            $semicolonCount = substr_count($line, ';');

            if ($semicolonCount > $commaCount) {
                return ';';
            }
            if ($commaCount > 0) {
                return ',';
            }
        }

        return ',';
    }

    private function parseOptionalDate(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $formats = ['Y-m-d', 'd/m/Y', 'd.m.Y'];
        foreach ($formats as $format) {
            $dt = DateTimeImmutable::createFromFormat($format, $value);
            if ($dt instanceof DateTimeImmutable) {
                return $dt->format('Y-m-d');
            }
        }

        throw new \InvalidArgumentException(sprintf('Date invalide (%s). Format attendu: YYYY-MM-DD.', $value));
    }
}
