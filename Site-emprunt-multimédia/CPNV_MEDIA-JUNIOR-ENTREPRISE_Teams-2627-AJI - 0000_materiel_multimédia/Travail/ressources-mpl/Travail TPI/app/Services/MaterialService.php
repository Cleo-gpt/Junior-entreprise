<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class MaterialService
{
    private PDO $connection;
    private ReservationService $reservations;

    public function __construct()
    {
        $this->connection = Database::connection();
        $this->reservations = new ReservationService();
    }

    public function availabilityForPeriod(string $id, string $startDate, string $endDate, ?string $excludeReservationId = null): int
    {
        $material = $this->find($id);

        if (!$material) {
            return 0;
        }

        $reserved = $this->reservations->reservedQuantityForMaterial(
            $id,
            $startDate,
            $endDate,
            $excludeReservationId
        );

        return max(0, $material['quantity_total'] - $reserved);
    }

    public function reservationsSummary(string $id, string $startDate, string $endDate): array
    {
        return [
            'overlaps' => $this->reservations->reservationsForMaterialBetween($id, $startDate, $endDate),
            'upcoming' => $this->reservations->upcomingReservationsForMaterial($id),
        ];
    }

    public function all(): array
    {
        // Check if damaged_items column exists
        try {
            $sql = 'SELECT id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items
                    FROM materials
                    ORDER BY name';
            $stmt = $this->connection->query($sql);
        } catch (\PDOException $e) {
            // Fallback without damaged_items if column doesn't exist
            $sql = 'SELECT id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost
                    FROM materials
                    ORDER BY name';
            $stmt = $this->connection->query($sql);
        }

        $materials = $stmt->fetchAll();

        return array_map(
            fn($material) => $this->formatMaterial($material),
            $materials
        );
    }

    public function find(string $id): ?array
    {
        try {
            $stmt = $this->connection->prepare(
                'SELECT id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items
                 FROM materials
                 WHERE id = :id'
            );
        } catch (\PDOException $e) {
            $stmt = $this->connection->prepare(
                'SELECT id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost
                 FROM materials
                 WHERE id = :id'
            );
        }

        $stmt->execute(['id' => $id]);
        $material = $stmt->fetch();

        return $material ? $this->formatMaterial($material) : null;
    }

    public function create(array $data): array
    {
        $payload = $this->validatePayload($data);
        $payload['quantity_available'] = $payload['quantity_total'];
        $payload['status'] = $payload['quantity_total'] > 0 ? 'available' : 'unavailable';

        // Check if damaged_items column exists
        try {
            $stmt = $this->connection->prepare(
                'INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items)
                 VALUES (:id, :name, :description, :quantity_total, :quantity_available, :status, :cover_image, :gallery, :tracking_mode, :identifiers, :replacement_cost, :damaged_items)'
            );

            $stmt->execute([
                'id' => $payload['id'],
                'name' => $payload['name'],
                'description' => $payload['description'],
                'quantity_total' => $payload['quantity_total'],
                'quantity_available' => $payload['quantity_available'],
                'status' => $payload['status'],
                'cover_image' => $payload['cover_image'],
                'gallery' => json_encode($payload['gallery'], JSON_UNESCAPED_UNICODE),
                'tracking_mode' => $payload['tracking_mode'],
                'identifiers' => json_encode($payload['identifiers'], JSON_UNESCAPED_UNICODE),
                'replacement_cost' => $payload['replacement_cost'],
                'damaged_items' => json_encode($payload['damaged_items'], JSON_UNESCAPED_UNICODE),
            ]);
        } catch (\PDOException $e) {
            // Fallback without damaged_items
            $stmt = $this->connection->prepare(
                'INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost)
                 VALUES (:id, :name, :description, :quantity_total, :quantity_available, :status, :cover_image, :gallery, :tracking_mode, :identifiers, :replacement_cost)'
            );

            $stmt->execute([
                'id' => $payload['id'],
                'name' => $payload['name'],
                'description' => $payload['description'],
                'quantity_total' => $payload['quantity_total'],
                'quantity_available' => $payload['quantity_available'],
                'status' => $payload['status'],
                'cover_image' => $payload['cover_image'],
                'gallery' => json_encode($payload['gallery'], JSON_UNESCAPED_UNICODE),
                'tracking_mode' => $payload['tracking_mode'],
                'identifiers' => json_encode($payload['identifiers'], JSON_UNESCAPED_UNICODE),
                'replacement_cost' => $payload['replacement_cost'],
            ]);
        }

        return $this->find($payload['id']);
    }

    public function update(string $id, array $data): array
    {
        $existing = $this->find($id);

        if (!$existing) {
            throw new \InvalidArgumentException('Matériel introuvable.');
        }

        $data['id'] = $id;

        if (!array_key_exists('cover_image', $data)) {
            $data['cover_image'] = $existing['cover_image'] ?? null;
        }

        if (!array_key_exists('gallery', $data)) {
            $data['gallery'] = $existing['gallery'] ?? [];
        }

        if (!array_key_exists('damaged_items', $data) && !array_key_exists('damaged', $data)) {
            $data['damaged_items'] = $existing['damaged_items'] ?? [];
        }

        $payload = $this->validatePayload($data, true, $id);

        $reserved = max(0, (int) $existing['quantity_total'] - (int) $existing['quantity_available']);

        if ($payload['quantity_total'] < $reserved) {
            throw new \InvalidArgumentException('Impossible de définir une quantité totale inférieure aux exemplaires déjà réservés.');
        }

        $payload['quantity_available'] = $payload['quantity_total'] - $reserved;
        $payload['status'] = $payload['quantity_available'] > 0 ? 'available' : 'unavailable';

        try {
            $stmt = $this->connection->prepare(
                'UPDATE materials
                 SET name = :name,
                     description = :description,
                     quantity_total = :quantity_total,
                     quantity_available = :quantity_available,
                     status = :status,
                     cover_image = :cover_image,
                     gallery = :gallery,
                     tracking_mode = :tracking_mode,
                     identifiers = :identifiers,
                     replacement_cost = :replacement_cost,
                     damaged_items = :damaged_items
                 WHERE id = :id'
            );

            $stmt->execute([
                'name' => $payload['name'],
                'description' => $payload['description'],
                'quantity_total' => $payload['quantity_total'],
                'quantity_available' => $payload['quantity_available'],
                'status' => $payload['status'],
                'cover_image' => $payload['cover_image'],
                'gallery' => json_encode($payload['gallery'], JSON_UNESCAPED_UNICODE),
                'tracking_mode' => $payload['tracking_mode'],
                'identifiers' => json_encode($payload['identifiers'], JSON_UNESCAPED_UNICODE),
                'replacement_cost' => $payload['replacement_cost'],
                'damaged_items' => json_encode($payload['damaged_items'], JSON_UNESCAPED_UNICODE),
                'id' => $id,
            ]);
        } catch (\PDOException $e) {
            // Fallback without damaged_items
            $stmt = $this->connection->prepare(
                'UPDATE materials
                 SET name = :name,
                     description = :description,
                     quantity_total = :quantity_total,
                     quantity_available = :quantity_available,
                     status = :status,
                     cover_image = :cover_image,
                     gallery = :gallery,
                     tracking_mode = :tracking_mode,
                     identifiers = :identifiers,
                     replacement_cost = :replacement_cost
                 WHERE id = :id'
            );

            $stmt->execute([
                'name' => $payload['name'],
                'description' => $payload['description'],
                'quantity_total' => $payload['quantity_total'],
                'quantity_available' => $payload['quantity_available'],
                'status' => $payload['status'],
                'cover_image' => $payload['cover_image'],
                'gallery' => json_encode($payload['gallery'], JSON_UNESCAPED_UNICODE),
                'tracking_mode' => $payload['tracking_mode'],
                'identifiers' => json_encode($payload['identifiers'], JSON_UNESCAPED_UNICODE),
                'replacement_cost' => $payload['replacement_cost'],
                'id' => $id,
            ]);
        }

        return $this->find($id);
    }

    public function decrementAvailability(string $id, int $quantity): void
    {
        $quantity = max(0, $quantity);
        if ($quantity === 0) {
            return;
        }

        $material = $this->find($id);

        if (!$material) {
            return;
        }

        $newAvailability = max(0, $material['quantity_available'] - $quantity);
        $status = $newAvailability > 0 ? 'available' : 'unavailable';

        $stmt = $this->connection->prepare(
            'UPDATE materials
             SET quantity_available = :quantity_available, status = :status
             WHERE id = :id'
        );

        $stmt->execute([
            'quantity_available' => $newAvailability,
            'status' => $status,
            'id' => $id,
        ]);
    }

    public function increaseAvailability(string $id, int $quantity): void
    {
        $quantity = max(0, $quantity);
        if ($quantity === 0) {
            return;
        }

        $material = $this->find($id);

        if (!$material) {
            return;
        }

        $newAvailability = min(
            $material['quantity_total'],
            $material['quantity_available'] + $quantity
        );
        $status = $newAvailability > 0 ? 'available' : 'unavailable';

        $stmt = $this->connection->prepare(
            'UPDATE materials
             SET quantity_available = :quantity_available, status = :status
             WHERE id = :id'
        );

        $stmt->execute([
            'quantity_available' => $newAvailability,
            'status' => $status,
            'id' => $id,
        ]);
    }

    private function validatePayload(array $data, bool $isUpdate = false, ?string $existingId = null): array
    {
        $rawId = $data['id'] ?? $existingId ?? '';
        $id = strtoupper(trim((string) $rawId));

        $name = trim((string) ($data['name'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));
        $quantityTotal = (int) ($data['quantity_total'] ?? 0);
        $coverImage = $data['cover_image'] ?? null;
        $gallery = $data['gallery'] ?? [];
        $trackingMode = strtolower(trim((string) ($data['tracking_mode'] ?? 'generic')));
        $identifiers = $data['identifiers'] ?? [];
        $replacementCostRaw = $data['replacement_cost'] ?? null;
        $damagedItems = $data['damaged_items'] ?? $data['damaged'] ?? [];

        if ($id === '' || $name === '' || $description === '') {
            throw new \InvalidArgumentException('Les champs identifiant, nom et description sont obligatoires.');
        }

        if (!$isUpdate && $this->find($id)) {
            throw new \InvalidArgumentException('Un matériel existe déjà avec cet identifiant.');
        }

        if ($isUpdate && $existingId && $id !== strtoupper($existingId)) {
            throw new \InvalidArgumentException('La modification de l’identifiant n’est pas autorisée.');
        }

        if ($quantityTotal < 0) {
            throw new \InvalidArgumentException('La quantité totale doit être positive.');
        }

        if (is_string($coverImage)) {
            $coverImage = trim($coverImage) ?: null;
        } elseif ($coverImage !== null) {
            throw new \InvalidArgumentException('L’image de couverture doit être une chaîne ou null.');
        }

        if (!in_array($trackingMode, ['generic', 'numbered'], true)) {
            throw new \InvalidArgumentException('Mode de suivi invalide.');
        }

        if (!is_array($gallery)) {
            throw new \InvalidArgumentException('La galerie doit être un tableau.');
        }

        $gallery = array_values(array_filter(array_map(
            fn($value) => is_string($value) ? trim($value) : '',
            $gallery
        )));
        $gallery = array_values(array_unique($gallery));

        if (!is_array($identifiers)) {
            $identifiers = [];
        }

        $identifiers = array_values(array_unique(array_filter(array_map(
            fn($value) => is_string($value) ? trim($value) : '',
            $identifiers
        ))));

        if ($trackingMode === 'numbered') {
            if (empty($identifiers)) {
                throw new \InvalidArgumentException('Les matériels numérotés doivent posséder au moins un identifiant individuel.');
            }

            if ($quantityTotal !== count($identifiers)) {
                throw new \InvalidArgumentException('Pour un matériel numéroté, la quantité totale doit correspondre au nombre d’identifiants fournis.');
            }
        } else {
            $identifiers = [];
        }

        $replacementCost = null;
        if ($replacementCostRaw !== null && $replacementCostRaw !== '') {
            $replacementCost = (float) $replacementCostRaw;
            if (!is_finite($replacementCost) || $replacementCost < 0) {
                throw new \InvalidArgumentException('Le prix indicatif doit être un nombre positif.');
            }
        }

        if (!is_array($damagedItems)) {
            $damagedItems = [];
        }

        return [
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'quantity_total' => $quantityTotal,
            'cover_image' => $coverImage,
            'gallery' => $gallery,
            'tracking_mode' => $trackingMode,
            'identifiers' => $identifiers,
            'replacement_cost' => $replacementCost,
            'damaged_items' => $damagedItems,
        ];
    }

    private function formatMaterial(array $material): array
    {
        $gallery = [];

        if (!empty($material['gallery'])) {
            $decoded = json_decode((string) $material['gallery'], true);
            $gallery = is_array($decoded) ? array_values(array_filter($decoded)) : [];
        }

        $trackingMode = strtolower($material['tracking_mode'] ?? 'generic');
        $identifiers = [];
        if (!empty($material['identifiers'])) {
            $decodedIdentifiers = json_decode((string) $material['identifiers'], true);
            $identifiers = is_array($decodedIdentifiers)
                ? array_values(array_filter(array_map('strval', $decodedIdentifiers)))
                : [];
        }

        $damagedItems = [];
        if (!empty($material['damaged_items'])) {
            $decodedDamaged = json_decode((string) $material['damaged_items'], true);
            $damagedItems = is_array($decodedDamaged) ? $decodedDamaged : [];
        }

        $cover = $material['cover_image'] ?? null;

        if (!$cover && $gallery) {
            $cover = $gallery[0];
        }

        return [
            'id' => $material['id'],
            'name' => $material['name'],
            'description' => $material['description'],
            'quantity_total' => (int) $material['quantity_total'],
            'quantity_available' => (int) $material['quantity_available'],
            'status' => $material['status'],
            'cover_image' => $cover,
            'gallery' => $gallery,
            'tracking_mode' => in_array($trackingMode, ['generic', 'numbered'], true) ? $trackingMode : 'generic',
            'identifiers' => $identifiers,
            'replacement_cost' => isset($material['replacement_cost']) ? (float) $material['replacement_cost'] : null,
            'damaged_items' => $damagedItems,
        ];
    }

    public function availableIdentifiers(string $id, string $startDate, string $endDate, ?string $excludeReservationId = null): array
    {
        $material = $this->find($id);

        if (!$material || ($material['tracking_mode'] ?? 'generic') !== 'numbered') {
            return [];
        }

        $all = $material['identifiers'] ?? [];

        if (empty($all)) {
            return [];
        }

        $assigned = $this->reservations->assignedIdentifiersForMaterial($id, $startDate, $endDate, $excludeReservationId);

        return array_values(array_diff($all, $assigned));
    }
}
