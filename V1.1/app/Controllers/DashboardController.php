<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\MaterialService;
use App\Services\ReservationService;

class DashboardController extends BaseController
{
    private ReservationService $reservations;
    private MaterialService $materials;

    public function __construct()
    {
        parent::__construct();
        $this->reservations = new ReservationService();
        $this->materials = new MaterialService();
    }

    public function index(): void
    {
        $user = $this->requireAuth();

        $reservations = $this->reservations->userReservations($user['id']);
        $materials = [];
        foreach ($reservations as $reservation) {
            foreach ($reservation['items'] as $item) {
                $materialId = $item['material_id'];
                if (!isset($materials[$materialId])) {
                    $materials[$materialId] = $this->materials->find($materialId);
                }
            }
        }

        $extensionOptions = [];
        foreach ($reservations as $reservation) {
            $extensionOptions[$reservation['id']] = $this->reservations->computeUserExtensionWindow(
                $reservation,
                function (string $materialId) use (&$materials) {
                    if (!isset($materials[$materialId])) {
                        $materials[$materialId] = $this->materials->find($materialId);
                    }
                    return $materials[$materialId];
                }
            );
        }

        $this->render('dashboard/index', [
            'user' => $user,
            'reservations' => $reservations,
            'materials' => $materials,
            'extensionOptions' => $extensionOptions,
        ]);
    }
}

