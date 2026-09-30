<?php

namespace App\Services;

use App\Core\Database;
use DateTimeImmutable;
use PDO;

class ReservationService
{
    private PDO $connection;
    private array $allowedStatuses = ['pending', 'approved', 'checked_out', 'returned', 'cancelled'];
    private array $userExtendableStatuses = ['pending', 'approved', 'checked_out'];

    public function __construct()
    {
        $this->connection = Database::connection();
    }

    public function all(): array
    {
        $stmt = $this->connection->query('SELECT * FROM reservations ORDER BY created_at DESC');
        $reservations = $stmt->fetchAll();

        return array_map(fn($r) => $this->hydrate($r), $reservations);
    }

    public function create(array $payload): array
    {
        $id = uniqid('res_', true);
        $status = $this->sanitizeStatus($payload['status'] ?? 'pending');

        $items = $this->sanitizeItems(
            $payload['items'] ?? null,
            [
                'material_id' => $payload['material_id'] ?? null,
                'quantity' => $payload['quantity'] ?? 1,
                'identifiers' => $payload['identifiers'] ?? [],
            ]
        );

        if (empty($items)) {
            throw new \InvalidArgumentException('Aucun matériel sélectionné pour cette réservation.');
        }

        $this->connection->beginTransaction();

        try {
            $stmt = $this->connection->prepare(
                'INSERT INTO reservations (id, user_id, start_date, end_date, comment, status, user_extension_used, created_at, updated_at)
                 VALUES (:id, :user_id, :start_date, :end_date, :comment, :status, :user_extension_used, NOW(), NOW())'
            );

            $stmt->execute([
                'id' => $id,
                'user_id' => $payload['user_id'],
                'start_date' => $payload['start_date'],
                'end_date' => $payload['end_date'],
                'comment' => $payload['comment'] ?? '',
                'status' => $status,
                'user_extension_used' => (int) ($payload['user_extension_used'] ?? false),
            ]);

            $this->insertItems($id, $items);

            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }

        return $this->find($id);
    }

    public function userReservations(string $userId): array
    {
        $stmt = $this->connection->prepare('SELECT * FROM reservations WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $userId]);

        return array_map(fn($r) => $this->hydrate($r), $stmt->fetchAll());
    }

    public function find(string $id): ?array
    {
        $stmt = $this->connection->prepare('SELECT * FROM reservations WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $reservation = $stmt->fetch();

        return $reservation ? $this->hydrate($reservation) : null;
    }

    public function update(string $id, array $payload): ?array
    {
        $existing = $this->find($id);
        if (!$existing) {
            return null;
        }

        $items = $this->sanitizeItems(
            $payload['items'] ?? $existing['items'],
            $existing['items']
        );

        if (empty($items)) {
            throw new \InvalidArgumentException('Une réservation doit contenir au moins un matériel.');
        }

        $this->connection->beginTransaction();

        try {
            $fields = [];
            $params = ['id' => $id];

            if (isset($payload['start_date'])) {
                $fields[] = 'start_date = :start_date';
                $params['start_date'] = $payload['start_date'];
            }
            if (isset($payload['end_date'])) {
                $fields[] = 'end_date = :end_date';
                $params['end_date'] = $payload['end_date'];
            }
            if (isset($payload['comment'])) {
                $fields[] = 'comment = :comment';
                $params['comment'] = $payload['comment'];
            }
            if (isset($payload['status'])) {
                $fields[] = 'status = :status';
                $params['status'] = $this->sanitizeStatus($payload['status']);
            }
            if (array_key_exists('user_extension_used', $payload)) {
                $fields[] = 'user_extension_used = :user_extension_used';
                $params['user_extension_used'] = (int) $payload['user_extension_used'];
            }

            if (!empty($fields)) {
                $fields[] = 'updated_at = NOW()';
                $sql = 'UPDATE reservations SET ' . implode(', ', $fields) . ' WHERE id = :id';
                $this->connection->prepare($sql)->execute($params);
            }

            // Update items: delete all and recreate (simplest strategy to handle changes)
            $this->connection->prepare('DELETE FROM reservation_items WHERE reservation_id = :id')->execute(['id' => $id]);
            $this->insertItems($id, $items);

            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }

        return $this->find($id);
    }

    public function delete(string $id): ?array
    {
        $existing = $this->find($id);
        if (!$existing) {
            return null;
        }

        $stmt = $this->connection->prepare('DELETE FROM reservations WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $existing;
    }

    public function restore(array $reservation): ?array
    {
        // Assuming $reservation comes from TrashService and has the structure we need
        // We treat it like a create but with existing ID

        $id = $reservation['id'];
        if ($this->find($id)) {
            return $this->find($id);
        }

        $this->connection->beginTransaction();

        try {
            $stmt = $this->connection->prepare(
                'INSERT INTO reservations (id, user_id, start_date, end_date, comment, status, user_extension_used, created_at, updated_at)
                 VALUES (:id, :user_id, :start_date, :end_date, :comment, :status, :user_extension_used, :created_at, NOW())'
            );

            $stmt->execute([
                'id' => $id,
                'user_id' => $reservation['user_id'],
                'start_date' => $reservation['start_date'],
                'end_date' => $reservation['end_date'],
                'comment' => $reservation['comment'] ?? '',
                'status' => $reservation['status'],
                'user_extension_used' => (int) ($reservation['user_extension_used'] ?? false),
                'created_at' => $reservation['created_at'] ?? date('Y-m-d H:i:s'),
            ]);

            $this->insertItems($id, $reservation['items'] ?? []);

            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }

        return $this->find($id);
    }

    public function allowedStatuses(): array
    {
        return $this->allowedStatuses;
    }

    public function blockingStatuses(): array
    {
        return ['pending', 'approved', 'checked_out'];
    }

    public function getIncidents(): array
    {
        $sql = "SELECT r.id as reservation_id, r.user_id, r.end_date, r.updated_at,
                       ri.material_id, ri.quantity, ri.identifiers, ri.return_status, ri.return_condition, ri.damaged_identifiers
                FROM reservations r
                JOIN reservation_items ri ON ri.reservation_id = r.id
                WHERE ri.return_status IN ('damaged', 'broken', 'lost')
                ORDER BY r.updated_at DESC";

        $stmt = $this->connection->query($sql);
        $rows = $stmt->fetchAll();

        return array_map(function ($row) {
            $allIdentifiers = json_decode($row['identifiers'] ?? '[]', true);
            $damagedIdentifiers = json_decode($row['damaged_identifiers'] ?? '[]', true);

            // If we have specific damaged identifiers, use them. 
            // Otherwise, if it's numbered tracking but no specific damaged IDs are set (legacy or user error), 
            // we might fallback to showing all (or none, depending on preference).
            // Let's prefer damagedIdentifiers if not empty.
            $displayIdentifiers = !empty($damagedIdentifiers) ? $damagedIdentifiers : $allIdentifiers;

            return [
                'reservation_id' => $row['reservation_id'],
                'user_id' => $row['user_id'],
                'date' => $row['end_date'] ?: $row['updated_at'],
                'material_id' => $row['material_id'],
                'quantity' => (int) $row['quantity'],
                'identifiers' => $displayIdentifiers,
                'status' => $row['return_status'],
                'condition' => $row['return_condition'],
            ];
        }, $rows);
    }

    public function reservedQuantityForMaterial(
        string $materialId,
        string $startDate,
        string $endDate,
        ?string $excludeReservationId = null
    ): int {
        $startDate = (new DateTimeImmutable($startDate))->format('Y-m-d');
        $endDate = (new DateTimeImmutable($endDate))->format('Y-m-d');

        // 1. Count permanently lost/broken items from returned reservations
        $sqlLost = "SELECT SUM(ri.quantity) 
                    FROM reservations r
                    JOIN reservation_items ri ON ri.reservation_id = r.id
                    WHERE r.status = 'returned'
                      AND ri.material_id = :mid
                      AND ri.return_status IN ('broken', 'lost')";

        if ($excludeReservationId) {
            $sqlLost .= " AND r.id != :exclude";
        }

        $paramsLost = ['mid' => $materialId];
        if ($excludeReservationId)
            $paramsLost['exclude'] = $excludeReservationId;

        $stmtLost = $this->connection->prepare($sqlLost);
        $stmtLost->execute($paramsLost);
        $lostQty = (int) $stmtLost->fetchColumn();

        // 2. Count overlapping active reservations
        $sqlActive = "SELECT SUM(ri.quantity)
                      FROM reservations r
                      JOIN reservation_items ri ON ri.reservation_id = r.id
                      WHERE r.status IN ('pending', 'approved', 'checked_out')
                        AND ri.material_id = :mid
                        AND r.start_date <= :end_date
                        AND r.end_date >= :start_date";

        if ($excludeReservationId) {
            $sqlActive .= " AND r.id != :exclude";
        }

        $paramsActive = [
            'mid' => $materialId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        if ($excludeReservationId)
            $paramsActive['exclude'] = $excludeReservationId;

        $stmtActive = $this->connection->prepare($sqlActive);
        $stmtActive->execute($paramsActive);
        $activeQty = (int) $stmtActive->fetchColumn();

        return $lostQty + $activeQty;
    }

    public function reservationsForMaterialBetween(string $materialId, string $startDate, string $endDate): array
    {
        $startDate = (new DateTimeImmutable($startDate))->format('Y-m-d');
        $endDate = (new DateTimeImmutable($endDate))->format('Y-m-d');

        $sql = "SELECT r.start_date, r.end_date, r.status, SUM(ri.quantity) as quantity
                FROM reservations r
                JOIN reservation_items ri ON ri.reservation_id = r.id
                WHERE ri.material_id = :mid
                  AND r.start_date <= :end_date
                  AND r.end_date >= :start_date
                GROUP BY r.id
                ORDER BY r.start_date";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'mid' => $materialId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        return $stmt->fetchAll();
    }

    public function upcomingReservationsForMaterial(string $materialId, int $limit = 5): array
    {
        $today = (new DateTimeImmutable('today'))->format('Y-m-d');

        $sql = "SELECT r.start_date, r.end_date, r.status, SUM(ri.quantity) as quantity
                FROM reservations r
                JOIN reservation_items ri ON ri.reservation_id = r.id
                WHERE ri.material_id = :mid
                  AND r.end_date >= :today
                GROUP BY r.id
                ORDER BY r.start_date ASC
                LIMIT :limit";

        // PDO LIMIT requires integer binding or direct value
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':mid', $materialId);
        $stmt->bindValue(':today', $today);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function computeUserExtensionWindow(array $reservation, callable $materialFetcher, int $maxDays = 7): array
    {
        // Logic remains mostly the same, just adapting to the fact that we have data
        // This method relies on 'reservedQuantityForMaterial' which we updated.
        // We can copy the logic from the original file as it is business logic, not storage logic.

        $result = [
            'max_days' => 0,
            'max_end_date' => null,
            'extension_start' => null,
            'reason' => null,
        ];

        if (($reservation['user_extension_used'] ?? false)) {
            $result['reason'] = 'already_used';
            return $result;
        }

        $status = $reservation['status'] ?? 'pending';
        if (!in_array($status, $this->userExtendableStatuses, true)) {
            $result['reason'] = 'status_not_extendable';
            return $result;
        }

        $currentEnd = $reservation['end_date'] ?? null;
        if (!$currentEnd) {
            $result['reason'] = 'missing_end_date';
            return $result;
        }

        $extensionStartDt = (new DateTimeImmutable($currentEnd))->modify('+1 day');
        $extensionStart = $extensionStartDt->format('Y-m-d');
        $result['extension_start'] = $extensionStart;

        if ($maxDays <= 0) {
            $result['reason'] = 'no_days_allowed';
            return $result;
        }

        $items = $reservation['items'] ?? [];
        if (empty($items)) {
            $result['reason'] = 'no_items';
            return $result;
        }

        $materials = [];
        foreach ($items as $item) {
            $materialId = $item['material_id'] ?? null;
            if (!$materialId) {
                $result['reason'] = 'invalid_item';
                return $result;
            }

            if (!isset($materials[$materialId])) {
                $materials[$materialId] = $materialFetcher($materialId);
            }

            if (!$materials[$materialId]) {
                $result['reason'] = 'material_not_found';
                return $result;
            }
        }

        $maxPossible = 0;

        for ($day = 1; $day <= $maxDays; $day++) {
            $candidateEndDt = $extensionStartDt->modify('+' . ($day - 1) . ' days');
            $candidateEnd = $candidateEndDt->format('Y-m-d');
            $canExtend = true;

            foreach ($items as $item) {
                $materialId = $item['material_id'];
                $quantity = max(1, (int) ($item['quantity'] ?? 0));
                $material = $materials[$materialId];
                $total = (int) ($material['quantity_total'] ?? 0);

                if ($total <= 0 || $quantity > $total) {
                    $canExtend = false;
                    break;
                }

                $otherReserved = $this->reservedQuantityForMaterial(
                    $materialId,
                    $extensionStart,
                    $candidateEnd,
                    $reservation['id']
                );

                $available = $total - $otherReserved;

                if ($available < $quantity) {
                    $canExtend = false;
                    break;
                }
            }

            if ($canExtend) {
                $maxPossible = $day;
            } else {
                break;
            }
        }

        if ($maxPossible > 0) {
            $result['max_days'] = $maxPossible;
            $result['max_end_date'] = $extensionStartDt
                ->modify('+' . ($maxPossible - 1) . ' days')
                ->format('Y-m-d');
        } else {
            $result['reason'] = $result['reason'] ?? 'no_capacity';
        }

        return $result;
    }

    public function assignedIdentifiersForMaterial(
        string $materialId,
        string $startDate,
        string $endDate,
        ?string $excludeReservationId = null
    ): array {
        $startDate = (new DateTimeImmutable($startDate))->format('Y-m-d');
        $endDate = (new DateTimeImmutable($endDate))->format('Y-m-d');

        // 1. Permanently lost/broken
        $sqlLost = "SELECT ri.identifiers
                    FROM reservations r
                    JOIN reservation_items ri ON ri.reservation_id = r.id
                    WHERE r.status = 'returned'
                      AND ri.material_id = :mid
                      AND ri.return_status IN ('broken', 'lost')";

        // 2. Active reservations overlapping
        $sqlActive = "SELECT ri.identifiers
                      FROM reservations r
                      JOIN reservation_items ri ON ri.reservation_id = r.id
                      WHERE r.status IN ('pending', 'approved', 'checked_out')
                        AND ri.material_id = :mid
                        AND r.start_date <= :end_date
                        AND r.end_date >= :start_date";

        $params = ['mid' => $materialId];
        $paramsActive = array_merge($params, ['start_date' => $startDate, 'end_date' => $endDate]);

        if ($excludeReservationId) {
            $sqlLost .= " AND r.id != :exclude";
            $sqlActive .= " AND r.id != :exclude";
            $params['exclude'] = $excludeReservationId;
            $paramsActive['exclude'] = $excludeReservationId;
        }

        $assigned = [];

        $stmt = $this->connection->prepare($sqlLost);
        $stmt->execute($params);
        foreach ($stmt->fetchAll() as $row) {
            $ids = json_decode($row['identifiers'] ?? '[]', true);
            if (is_array($ids)) {
                foreach ($ids as $id)
                    $assigned[$id] = true;
            }
        }

        $stmt = $this->connection->prepare($sqlActive);
        $stmt->execute($paramsActive);
        foreach ($stmt->fetchAll() as $row) {
            $ids = json_decode($row['identifiers'] ?? '[]', true);
            if (is_array($ids)) {
                foreach ($ids as $id)
                    $assigned[$id] = true;
            }
        }

        return array_keys($assigned);
    }

    private function hydrate(array $reservation): array
    {
        $reservation['user_extension_used'] = (bool) $reservation['user_extension_used'];

        // Load items
        $stmt = $this->connection->prepare('SELECT * FROM reservation_items WHERE reservation_id = :id');
        $stmt->execute(['id' => $reservation['id']]);
        $items = $stmt->fetchAll();

        $reservation['items'] = array_map(function ($item) {
            $item['identifiers'] = json_decode($item['identifiers'] ?? '[]', true);
            $item['damaged_identifiers'] = json_decode($item['damaged_identifiers'] ?? '[]', true);
            return $item;
        }, $items);

        // Backward compatibility for single-item view
        if (!empty($reservation['items'])) {
            $reservation['material_id'] = $reservation['items'][0]['material_id'];
            $reservation['quantity'] = $reservation['items'][0]['quantity'];
        } else {
            $reservation['material_id'] = null;
            $reservation['quantity'] = 0;
        }

        return $reservation;
    }

    private function insertItems(string $reservationId, array $items): void
    {
        // Try with damaged_identifiers column first (newer schema)
        try {
            $stmt = $this->connection->prepare(
                'INSERT INTO reservation_items (reservation_id, material_id, quantity, identifiers, return_status, return_condition, damaged_identifiers)
                 VALUES (:rid, :mid, :qty, :ids, :rstatus, :rcond, :damaged_ids)'
            );

            foreach ($items as $item) {
                $stmt->execute([
                    'rid' => $reservationId,
                    'mid' => $item['material_id'],
                    'qty' => $item['quantity'],
                    'ids' => json_encode($item['identifiers'] ?? []),
                    'rstatus' => $item['return_status'] ?? 'ok',
                    'rcond' => $item['return_condition'] ?? null,
                    'damaged_ids' => json_encode($item['damaged_identifiers'] ?? []),
                ]);
            }
        } catch (\PDOException $e) {
            // Log the error to understand why it failed
            error_log("[CRITICAL ERROR] Failed to insert with damaged_identifiers: " . $e->getMessage());
            throw $e; // Re-throw to prevent silent data loss
        }
    }

    /**
     * Public helper to insert a single reservation item
     * Used by AdminController when updating damaged items
     */
    public function insertReservationItem(string $reservationId, array $item): void
    {
        $this->insertItems($reservationId, [$item]);
    }

    private function sanitizeStatus(string $status): string
    {
        $status = strtolower(trim($status));
        return in_array($status, $this->allowedStatuses, true) ? $status : 'pending';
    }

    private function sanitizeItems(null|array $items, ?array $fallback): array
    {
        // Reuse original logic or simplified version
        $sanitized = [];
        $rawItems = (is_array($items) && !empty($items)) ? $items : ($fallback ? [$fallback] : []);

        // Flatten fallback if it's an array of items vs single item
        if (isset($rawItems[0]) && is_array($rawItems[0])) {
            // it's already a list of items
        } else {
            $rawItems = [$rawItems];
        }

        foreach ($rawItems as $item) {
            $materialId = strtoupper(trim((string) ($item['material_id'] ?? '')));
            $quantity = max(1, (int) ($item['quantity'] ?? 0));

            if ($materialId === '')
                continue;

            if (!isset($sanitized[$materialId])) {
                $sanitized[$materialId] = [
                    'material_id' => $materialId,
                    'quantity' => 0,
                    'identifiers' => [],
                    'return_status' => $item['return_status'] ?? 'ok',
                    'return_condition' => $item['return_condition'] ?? null,
                    'damaged_identifiers' => $item['damaged_identifiers'] ?? [],
                ];
            }

            $sanitized[$materialId]['quantity'] += $quantity;

            $ids = $item['identifiers'] ?? [];
            if (is_string($ids))
                $ids = explode(',', $ids);
            if (is_array($ids)) {
                $sanitized[$materialId]['identifiers'] = array_unique(array_merge(
                    $sanitized[$materialId]['identifiers'],
                    $ids
                ));
            }

            // Merge damaged identifiers if multiple entries for same material (unlikely but safer)
            $damagedIds = $item['damaged_identifiers'] ?? [];
            if (is_array($damagedIds)) {
                $sanitized[$materialId]['damaged_identifiers'] = array_unique(array_merge(
                    $sanitized[$materialId]['damaged_identifiers'],
                    $damagedIds
                ));
            }
        }

        return array_values($sanitized);
    }
}
