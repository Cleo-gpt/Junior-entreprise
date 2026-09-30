<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\MaterialService;
use App\Services\ReservationService;

class ReservationController extends BaseController
{
    private ReservationService $reservations;
    private MaterialService $materials;

    public function __construct()
    {
        parent::__construct();
        $this->reservations = new ReservationService();
        $this->materials = new MaterialService();
    }

    public function store(): void
    {
        $user = $this->requireAuth();

        $materialId = strtoupper(trim($_POST['material_id'] ?? ''));
        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
        $comment = trim($_POST['comment'] ?? '');

        if ($materialId === '' || !$startDate || !$endDate) {
            $this->session->flash('error', 'Veuillez sélectionner un matériel, une quantité et des dates.');
            $this->redirect('/materials');
        }

        $material = $this->materials->find($materialId);

        if (!$material) {
            $this->session->flash('error', 'Matériel introuvable.');
            $this->redirect('/materials');
        }

        if ($startDate > $endDate) {
            $this->session->flash('error', 'La date de fin doit être postérieure à la date de début.');
            $this->redirect('/materials');
        }

        $available = $this->materials->availabilityForPeriod($materialId, $startDate, $endDate);

        if ($available < $quantity) {
            $message = $available > 0
                ? "Stock insuffisant pour cette période : {$available} exemplaire(s) disponible(s)."
                : 'Ce matériel est déjà entièrement réservé sur la période choisie.';

            $this->session->flash('error', $message);
            $this->redirect('/materials?start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate));
        }

        try {
            $this->reservations->create([
                'user_id' => $user['id'],
                'items' => [
                    [
                        'material_id' => $materialId,
                        'quantity' => $quantity,
                    ],
                ],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'comment' => $comment,
            ]);
        } catch (\InvalidArgumentException $e) {
            $this->session->flash('error', $e->getMessage());
            $this->redirect('/materials');
        }

        $this->session->flash('success', 'Réservation enregistrée. Un responsable validera votre demande.');
        $this->redirect('/');
    }

    public function extend(): void
    {
        $user = $this->requireAuth();

        $reservationId = trim($_POST['reservation_id'] ?? '');
        $newEndDateInput = $_POST['new_end_date'] ?? '';

        if ($reservationId === '' || $newEndDateInput === '') {
            $this->session->flash('error', 'Données de prolongation manquantes.');
            $this->redirect('/');
        }

        $newEndDate = $this->normalizeDateInput($newEndDateInput);

        if ($newEndDate === null) {
            $this->session->flash('error', 'Date de fin invalide.');
            $this->redirect('/');
        }

        $reservation = $this->reservations->find($reservationId);

        if (!$reservation || ($reservation['user_id'] ?? null) !== ($user['id'] ?? null)) {
            $this->session->flash('error', 'Réservation introuvable.');
            $this->redirect('/');
        }

        if (in_array($reservation['status'] ?? 'pending', ['cancelled', 'returned'], true)) {
            $this->session->flash('error', 'Cette réservation ne peut plus être prolongée.');
            $this->redirect('/');
        }

        if (($reservation['user_extension_used'] ?? false) === true) {
            $this->session->flash('error', 'La prolongation utilisateur a déjà été utilisée.');
            $this->redirect('/');
        }

        $window = $this->reservations->computeUserExtensionWindow(
            $reservation,
            fn (string $materialId) => $this->materials->find($materialId)
        );

        $extensionStart = $window['extension_start'];
        $maxEndDate = $window['max_end_date'];

        if (($window['max_days'] ?? 0) <= 0 || !$extensionStart || !$maxEndDate) {
            $this->session->flash('error', 'Aucune prolongation n’est disponible pour cette réservation.');
            $this->redirect('/');
        }

        if ($newEndDate < $extensionStart || $newEndDate > $maxEndDate) {
            $formattedMax = date('d.m.Y', strtotime($maxEndDate));
            $this->session->flash('error', 'Vous ne pouvez prolonger que jusqu’au ' . $formattedMax . '.');
            $this->redirect('/');
        }

        $this->reservations->update($reservationId, [
            'end_date' => $newEndDate,
            'user_extension_used' => true,
        ]);

        $this->session->flash(
            'success',
            'Réservation prolongée jusqu’au ' . date('d.m.Y', strtotime($newEndDate)) . '.'
        );

        $this->redirect('/');
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

