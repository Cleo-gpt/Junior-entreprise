<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\MaterialService;

class MaterialController extends BaseController
{
    private MaterialService $materials;

    public function __construct()
    {
        parent::__construct();
        $this->materials = new MaterialService();
    }

    public function index(): void
    {
        $user = $this->requireAuth();

        $materials = $this->materials->all();
        $startInput = $_GET['start_date'] ?? null;
        $endInput = $_GET['end_date'] ?? null;

        $defaultStart = (new \DateTimeImmutable('today'))->format('Y-m-d');
        $defaultEnd = (new \DateTimeImmutable('today +7 days'))->format('Y-m-d');

        $startDate = $this->normalizeDateInput($startInput) ?? $defaultStart;
        $endDate = $this->normalizeDateInput($endInput) ?? $defaultEnd;

        if ($endDate < $startDate) {
            $endDate = $startDate;
        }

        $availability = [];
        $overlaps = [];
        $upcoming = [];

        foreach ($materials as $material) {
            $materialId = $material['id'];
            $availability[$materialId] = $this->materials->availabilityForPeriod($materialId, $startDate, $endDate);
            $summary = $this->materials->reservationsSummary($materialId, $startDate, $endDate);
            $overlaps[$materialId] = $summary['overlaps'];
            $upcoming[$materialId] = $summary['upcoming'];
        }

        $this->render('materials/index', [
            'user' => $user,
            'materials' => $materials,
            'selectedStart' => $startDate,
            'selectedEnd' => $endDate,
            'availability' => $availability,
            'overlaps' => $overlaps,
            'upcoming' => $upcoming,
        ]);
    }

    private function normalizeDateInput(?string $value): ?string
    {
        $value = $value !== null ? trim($value) : null;

        if ($value === null || $value === '') {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $date ? $date->format('Y-m-d') : null;
    }
}

