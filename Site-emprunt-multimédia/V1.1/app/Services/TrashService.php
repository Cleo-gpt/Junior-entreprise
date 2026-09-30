<?php

namespace App\Services;

use App\Core\Database;
use DateTimeImmutable;
use PDO;

class TrashService
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Database::connection();
    }

    public function all(): array
    {
        $stmt = $this->connection->query('SELECT * FROM trash ORDER BY deleted_at DESC');
        $entries = $stmt->fetchAll();

        return array_map(fn($entry) => $this->hydrate($entry), $entries);
    }

    public function find(string $trashId): ?array
    {
        $stmt = $this->connection->prepare('SELECT * FROM trash WHERE id = :id');
        $stmt->execute(['id' => $trashId]);
        $entry = $stmt->fetch();

        return $entry ? $this->hydrate($entry) : null;
    }

    public function store(string $type, string $entityId, array $payload, array $meta = []): array
    {
        $data = [
            'payload' => $payload,
            'meta' => [
                'deleted_by' => $meta['user_id'] ?? null,
                'deleted_by_name' => $meta['user_name'] ?? null,
                'deleted_by_role' => $meta['user_role'] ?? null,
            ],
        ];

        $stmt = $this->connection->prepare(
            'INSERT INTO trash (original_id, type, data, deleted_at)
             VALUES (:original_id, :type, :data, NOW())'
        );

        $stmt->execute([
            'original_id' => $entityId,
            'type' => $type,
            'data' => json_encode($data),
        ]);

        $id = $this->connection->lastInsertId();

        return $this->find((string) $id);
    }

    public function remove(string $trashId): ?array
    {
        $existing = $this->find($trashId);
        if (!$existing) {
            return null;
        }

        $stmt = $this->connection->prepare('DELETE FROM trash WHERE id = :id');
        $stmt->execute(['id' => $trashId]);

        return $existing;
    }

    private function hydrate(array $entry): array
    {
        $data = json_decode($entry['data'] ?? '[]', true);

        return [
            'id' => (string) $entry['id'],
            'type' => $entry['type'],
            'entity_id' => $entry['original_id'],
            'payload' => $data['payload'] ?? [],
            'meta' => $data['meta'] ?? [],
            'deleted_at' => $entry['deleted_at'],
        ];
    }
}
