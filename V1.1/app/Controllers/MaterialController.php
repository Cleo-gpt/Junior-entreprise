<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\CartService;
use App\Services\InventorySpaceService;
use App\Services\MaterialService;

class MaterialController extends BaseController
{
    private MaterialService $materials;
    private InventorySpaceService $spaces;
    private CartService $cart;

    public function __construct()
    {
        parent::__construct();
        $this->materials = new MaterialService();
        $this->spaces = new InventorySpaceService();
        $this->cart = new CartService();
    }

    public function index(): void
    {
        $user = $this->requireAuth();
        $spaceSlug = trim((string) ($_GET['space'] ?? ''));

        if ($spaceSlug !== '') {
            $this->showSpace($user, $spaceSlug);
            return;
        }

        $this->render('materials/index', [
            'user' => $user,
            'spaces' => $this->spaces->catalogSpaces(),
        ]);
    }

    private function showSpace(array $user, string $spaceSlug): void
    {
        $space = $this->spaces->findSpace($spaceSlug);

        if (!$space) {
            $this->session->flash('error', 'Espace matériel introuvable.');
            header('Location: ' . route('/materials'));
            exit;
        }

        $query = trim((string) ($_GET['q'] ?? ''));
        $products = $this->spaces->productsForSpace($spaceSlug, $query !== '' ? $query : null);

        $cartQuantities = [];
        foreach ($this->cart->items() as $item) {
            $cartQuantities[$item['material_id']] = $item['quantity'];
        }

        foreach ($products as &$product) {
            $inCart = (int) ($cartQuantities[$product['material_id']] ?? 0);
            $product['in_cart'] = $inCart;
            $product['remaining'] = max(0, (int) $product['quantity_available'] - $inCart);
        }
        unset($product);

        $this->render('materials/space', [
            'user' => $user,
            'space' => $space,
            'products' => $products,
            'query' => $query,
        ]);
    }
}
