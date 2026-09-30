<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\CartService;
use App\Services\MaterialService;
use App\Services\ReservationService;

class CartController extends BaseController
{
    private CartService $cart;
    private MaterialService $materials;
    private ReservationService $reservations;
    private ?string $defaultStartDate = null;
    private ?string $defaultEndDate = null;
    private ?string $horizonDate = null;

    public function __construct()
    {
        parent::__construct();
        $this->cart = new CartService();
        $this->materials = new MaterialService();
        $this->reservations = new ReservationService();
    }

    public function index(): void
    {
        $user = $this->requireAuth();

        $items = $this->cart->items();
        $detailed = [];
        $materials = [];

        foreach ($items as $item) {
            $material = $materials[$item['material_id']] ?? $this->materials->find($item['material_id']);

            if (!$material) {
                continue;
            }

            $materials[$material['id']] = $material;

            $detailed[] = [
                'material' => $material,
                'quantity' => $item['quantity'],
            ];
        }

        $this->render('cart/index', [
            'user' => $user,
            'items' => $detailed,
            'blockedDates' => $this->computeBlockedDates($detailed),
            'defaultStartDate' => $this->defaultStartDate,
            'defaultEndDate' => $this->defaultEndDate,
            'horizonDate' => $this->horizonDate,
        ]);
    }

    public function add(): void
    {
        $this->requireAuth();

        $materialId = strtoupper(trim($_POST['material_id'] ?? ''));
        $quantity = (int) ($_POST['quantity'] ?? 1);
        $redirect = $this->safeRedirect($_POST['redirect'] ?? '/materials');

        if ($materialId === '') {
            $this->session->flash('error', 'Matériel introuvable.');
            $this->redirect($redirect);
        }

        $material = $this->materials->find($materialId);

        if (!$material) {
            $this->session->flash('error', 'Matériel introuvable.');
            $this->redirect($redirect);
        }

        $quantity = max(1, $quantity);

        $totalStock = (int) ($material['quantity_available'] ?? $material['quantity_total'] ?? 0);

        $currentItems = $this->cart->items();
        $currentQuantity = 0;

        foreach ($currentItems as $item) {
            if ($item['material_id'] === $materialId) {
                $currentQuantity = $item['quantity'];
                break;
            }
        }

        if ($quantity + $currentQuantity > $totalStock) {
            $this->session->flash('error', 'Quantité indisponible. Stock restant : ' . max(0, $totalStock - $currentQuantity) . ' exemplaire(s).');
            $this->redirect($redirect);
        }

        $this->cart->add($materialId, $quantity);
        $this->session->flash('success', '1 exemplaire ajouté au panier.');
        $this->redirect($redirect);
    }

    public function update(): void
    {
        $this->requireAuth();

        $materialId = strtoupper(trim($_POST['material_id'] ?? ''));
        $quantity = (int) ($_POST['quantity'] ?? 1);

        if ($materialId === '') {
            $this->session->flash('error', 'Matériel introuvable.');
            $this->redirect('/cart');
        }

        $material = $this->materials->find($materialId);
        if (!$material) {
            $this->cart->remove($materialId);
            $this->session->flash('error', 'Matériel introuvable.');
            $this->redirect('/cart');
        }

        $quantity = max(0, $quantity);
        $totalStock = (int) ($material['quantity_total'] ?? 0);

        if ($quantity > $totalStock) {
            $this->session->flash('error', 'Quantité indisponible. Stock maximum : ' . $totalStock . ' exemplaire(s).');
            $this->redirect('/cart');
        }

        $this->cart->update($materialId, $quantity);
        $this->session->flash('success', 'Panier mis à jour.');
        $this->redirect('/cart');
    }

    public function remove(): void
    {
        $this->requireAuth();

        $materialId = strtoupper(trim($_POST['material_id'] ?? ''));
        if ($materialId !== '') {
            $this->cart->remove($materialId);
        }

        $this->session->flash('success', 'Matériel retiré du panier.');
        $this->redirect('/cart');
    }

    public function clear(): void
    {
        $this->requireAuth();

        $this->cart->clear();
        $this->session->flash('success', 'Panier vidé.');
        $this->redirect('/cart');
    }

    public function checkout(): void
    {
        $user = $this->requireAuth();

        $items = $this->cart->items();

        if (empty($items)) {
            $this->session->flash('error', 'Votre panier est vide.');
            $this->redirect('/cart');
        }

        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;
        $comment = trim($_POST['comment'] ?? '');

        if (!$startDate || !$endDate || $startDate > $endDate) {
            $this->session->flash('error', 'Veuillez sélectionner des dates valides.');
            $this->redirect('/cart');
        }

        foreach ($items as $item) {
            $materialId = strtoupper(trim($item['material_id']));
            $material = $this->materials->find($materialId);

            if (!$material) {
                $this->session->flash('error', 'Matériel introuvable : ' . htmlspecialchars($materialId));
                $this->redirect('/cart');
            }

            $available = $this->materials->availabilityForPeriod($materialId, $startDate, $endDate);
            if ($available < $item['quantity']) {
                $message = $available > 0
                    ? 'Stock insuffisant pour ' . htmlspecialchars($material['name']) . ' sur cette période. Disponibles : ' . $available . '.'
                    : 'Le matériel ' . htmlspecialchars($material['name']) . ' est déjà entièrement réservé sur cette période.';
                $this->session->flash('error', $message);
                $this->redirect('/cart');
            }
        }

        $reservation = $this->reservations->create([
            'user_id' => $user['id'],
            'items' => $items,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'comment' => $comment,
        ]);

        $this->cart->clear();
        $this->session->flash('success', 'Réservation enregistrée. Un responsable validera votre demande.');
        $this->redirect('/');
    }

    /**
     * @param array<int, array{material: array, quantity:int}> $items
     * @return string[]
     */
    private function computeBlockedDates(array $items): array
    {
        $this->defaultStartDate = null;
        $this->defaultEndDate = null;
        $this->horizonDate = null;

        if (empty($items)) {
            return [];
        }

        $today = new \DateTimeImmutable('today');
        $horizon = $today->modify('+6 months');
        $this->horizonDate = $horizon->format('Y-m-d');

        $interval = new \DateInterval('P1D');
        $blockedMap = [];
        $availabilityCache = [];

        for ($date = $today; $date <= $horizon; $date = $date->add($interval)) {
            $dateString = $date->format('Y-m-d');

            foreach ($items as $entry) {
                $material = $entry['material'];
                $materialId = $material['id'] ?? null;
                $quantity = (int) ($entry['quantity'] ?? 0);

                if (!$materialId || $quantity < 1) {
                    continue;
                }

                if (!isset($availabilityCache[$materialId][$dateString])) {
                    $availabilityCache[$materialId][$dateString] = $this->materials->availabilityForPeriod(
                        $materialId,
                        $dateString,
                        $dateString
                    );
                }

                if ($availabilityCache[$materialId][$dateString] < $quantity) {
                    $blockedMap[$dateString] = true;
                    break;
                }
            }

            if ($this->defaultStartDate === null && !isset($blockedMap[$dateString])) {
                $this->defaultStartDate = $dateString;
                $this->defaultEndDate = $dateString;
            }
        }

        $blockedDates = array_keys($blockedMap);
        sort($blockedDates);

        return $blockedDates;
    }

    private function safeRedirect(string $target): string
    {
        $target = trim($target);
        if ($target === '' || !str_starts_with($target, '/')) {
            return '/materials';
        }

        // Empêche les redirections externes
        if (str_contains($target, '://')) {
            return '/materials';
        }

        return $target;
    }
}

