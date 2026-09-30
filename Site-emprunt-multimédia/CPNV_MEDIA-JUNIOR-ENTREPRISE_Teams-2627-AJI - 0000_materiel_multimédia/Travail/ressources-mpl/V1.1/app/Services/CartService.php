<?php

namespace App\Services;

use App\Core\Session;

class CartService
{
    private Session $session;
    private const CART_KEY = 'material_cart';

    public function __construct()
    {
        $this->session = new Session();
    }

    public function items(): array
    {
        $items = $this->session->get(self::CART_KEY, []);

        return array_values(array_map(function ($item) {
            return [
                'material_id' => $item['material_id'],
                'quantity' => max(1, (int) $item['quantity']),
            ];
        }, $items));
    }

    public function add(string $materialId, int $quantity): void
    {
        $materialId = strtoupper(trim($materialId));
        $quantity = max(1, $quantity);
        $items = $this->indexByMaterial();

        if (isset($items[$materialId])) {
            $items[$materialId]['quantity'] += $quantity;
        } else {
            $items[$materialId] = [
                'material_id' => $materialId,
                'quantity' => $quantity,
            ];
        }

        $this->session->set(self::CART_KEY, array_values($items));
    }

    public function update(string $materialId, int $quantity): void
    {
        $materialId = strtoupper(trim($materialId));
        $items = $this->indexByMaterial();

        if (!isset($items[$materialId])) {
            return;
        }

        if ($quantity <= 0) {
            unset($items[$materialId]);
        } else {
            $items[$materialId]['quantity'] = $quantity;
        }

        $this->session->set(self::CART_KEY, array_values($items));
    }

    public function remove(string $materialId): void
    {
        $materialId = strtoupper(trim($materialId));
        $items = $this->indexByMaterial();

        if (isset($items[$materialId])) {
            unset($items[$materialId]);
        }

        $this->session->set(self::CART_KEY, array_values($items));
    }

    public function clear(): void
    {
        $this->session->remove(self::CART_KEY);
    }

    private function indexByMaterial(): array
    {
        $indexed = [];

        foreach ($this->session->get(self::CART_KEY, []) as $item) {
            $materialId = strtoupper(trim((string) ($item['material_id'] ?? '')));
            if ($materialId === '') {
                continue;
            }

            $indexed[$materialId] = [
                'material_id' => $materialId,
                'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
            ];
        }

        return $indexed;
    }
}

