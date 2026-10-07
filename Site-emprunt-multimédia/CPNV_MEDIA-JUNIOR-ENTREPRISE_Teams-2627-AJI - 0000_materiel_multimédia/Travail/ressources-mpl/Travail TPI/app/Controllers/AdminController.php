<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\MaterialService;
use App\Services\NotificationService;
use App\Services\ReservationService;
use App\Services\TrashService;
use App\Services\UserService;

class AdminController extends BaseController
{
    private MaterialService $materials;
    private NotificationService $notifications;
    private ReservationService $reservations;
    private TrashService $trash;
    private UserService $users;
    private int $maxUploadSize = 5242880; // 5 MB
    private \PDO $connection;

    public function __construct()
    {
        parent::__construct();
        $this->materials = new MaterialService();
        $this->notifications = new NotificationService();
        $this->reservations = new ReservationService();
        $this->users = new UserService();
        $this->trash = new TrashService();
        $this->connection = \App\Core\Database::connection();
    }

    public function tools(): void
    {
        $user = $this->requireAuth(['admin', 'responsable']);

        $this->render('admin/tools', [
            'user' => $user,
            'materials' => $this->materials->all(),
        ]);
    }

    public function users(): void
    {
        $user = $this->requireAuth(['admin', 'responsable']);

        $this->render('admin/users', [
            'user' => $user,
            'users' => $this->users->all(),
            'roles' => $this->users->availableRoles(),
            'statuses' => $this->users->availableStatuses(),
            'whitelist' => $this->users->whitelist(),
            'roleLabels' => config('auth.role_labels', []),
        ]);
    }

    public function reservations(): void
    {
        $user = $this->requireAuth(['admin', 'responsable']);

        $reservations = $this->reservations->all();
        $statusFilter = strtolower(trim($_GET['status'] ?? 'all'));
        if ($statusFilter !== 'all' && $statusFilter !== '') {
            $reservations = array_values(array_filter(
                $reservations,
                static fn($reservation) => strtolower($reservation['status'] ?? 'pending') === $statusFilter
            ));
        }
        $materials = [];
        $users = [];

        foreach ($reservations as $reservation) {
            foreach ($reservation['items'] as $item) {
                $materialId = $item['material_id'] ?? null;
                if ($materialId && !isset($materials[$materialId])) {
                    $materials[$materialId] = $this->materials->find($materialId);
                }
            }

            $userId = $reservation['user_id'] ?? null;
            if ($userId && !isset($users[$userId])) {
                $users[$userId] = $this->users->findById($userId);
            }
        }

        $statusLabels = [
            'pending' => 'En attente',
            'approved' => 'Validée',
            'checked_out' => 'Retirée',
            'returned' => 'Restituée',
            'cancelled' => 'Annulée',
        ];

        $identifierOptions = [];
        foreach ($reservations as $reservation) {
            foreach ($reservation['items'] as $item) {
                $materialId = $item['material_id'] ?? null;
                if (!$materialId) {
                    continue;
                }

                $material = $materials[$materialId] ?? null;
                if (!$material || ($material['tracking_mode'] ?? 'generic') !== 'numbered') {
                    continue;
                }

                $available = [];
                if (!empty($reservation['start_date']) && !empty($reservation['end_date'])) {
                    $available = $this->materials->availableIdentifiers(
                        $materialId,
                        $reservation['start_date'],
                        $reservation['end_date'],
                        $reservation['id']
                    );
                }
                $available = array_values($available);
                sort($available, SORT_NATURAL);

                $allIdentifiers = $material['identifiers'] ?? [];
                $allIdentifiers = array_values($allIdentifiers);
                sort($allIdentifiers, SORT_NATURAL);

                $identifierOptions[$reservation['id']][$materialId] = [
                    'available' => $available,
                    'assigned' => $item['identifiers'] ?? [],
                    'all' => $allIdentifiers,
                ];
            }
        }

        $this->render('admin/reservations', [
            'user' => $user,
            'reservations' => $reservations,
            'materials' => $materials,
            'users' => $users,
            'statuses' => $this->reservations->allowedStatuses(),
            'statusLabels' => $statusLabels,
            'identifierOptions' => $identifierOptions,
            'statusFilter' => $statusFilter,
        ]);
    }

    public function incidents(): void
    {
        $user = $this->requireAuth(['admin', 'responsable']);

        $incidents = $this->reservations->getIncidents();

        foreach ($incidents as &$incident) {
            $material = $this->materials->find($incident['material_id']);
            $incident['material_name'] = $material['name'] ?? $incident['material_id'];
            $incident['replacement_cost'] = $material['replacement_cost'] ?? null;

            $userInfo = $this->users->findById($incident['user_id']);
            $incident['user_name'] = $userInfo['name'] ?? 'Inconnu';
            $incident['user_email'] = $userInfo['email'] ?? '';
        }
        unset($incident);

        $this->render('admin/incidents', [
            'user' => $user,
            'incidents' => $incidents,
        ]);
    }

    public function trash(): void
    {
        $user = $this->requireAuth(['admin']);

        $entries = $this->trash->all();

        $this->render('admin/trash', [
            'user' => $user,
            'entries' => $entries,
        ]);
    }

    public function storeMaterial(): void
    {
        $this->requireAuth(['admin', 'responsable']);

        if ($error = $this->checkUploadLimits()) {
            $this->session->flash('error', $error);
            $this->redirect('/admin/tools');
        }

        try {
            $images = $this->processMaterialImages();
            $trackingMode = $_POST['tracking_mode'] ?? 'generic';
            $identifiers = $this->parseIdentifiers($_POST['identifiers'] ?? '');
            $quantityTotal = (int) ($_POST['quantity_total'] ?? 0);
            $replacementCost = $_POST['replacement_cost'] ?? null;

            if ($replacementCost !== null && $replacementCost !== '') {
                $replacementCost = (float) $replacementCost;
            } else {
                $replacementCost = null;
            }

            if ($trackingMode === 'numbered') {
                $materialId = $_POST['material_id'] ?? '';
                $identifiers = $this->generateIdentifiers($materialId, $quantityTotal, $identifiers);
            } else {
                $identifiers = [];
            }

            $material = $this->materials->create([
                'id' => $_POST['material_id'] ?? '',
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'quantity_total' => $quantityTotal,
                'cover_image' => $images['cover'],
                'gallery' => $images['gallery'],
                'tracking_mode' => $trackingMode,
                'identifiers' => $identifiers,
                'replacement_cost' => $replacementCost,
            ]);
        } catch (\InvalidArgumentException $e) {
            $this->session->flash('error', $e->getMessage());
            $this->redirect('/admin/tools');
            return;
        } catch (\RuntimeException $e) {
            $this->session->flash('error', $e->getMessage());
            $this->redirect('/admin/tools');
            return;
        }

        $this->session->flash('success', sprintf('Matériel %s ajouté avec succès.', $material['name']));

        $this->redirect('/admin/tools');
    }

    public function editMaterial(): void
    {
        $this->requireAuth(['admin', 'responsable']);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->session->flash('error', 'Matériel introuvable.');
            $this->redirect('/admin/tools');
        }

        $material = $this->materials->find($id);

        if (!$material) {
            $this->session->flash('error', 'Matériel introuvable.');
            $this->redirect('/admin/tools');
        }

        $this->render('admin/material_edit', [
            'material' => $material,
        ]);
    }

    public function updateMaterial(): void
    {
        $this->requireAuth(['admin', 'responsable']);

        $id = $_POST['material_id'] ?? null;
        if (!$id) {
            $this->session->flash('error', 'Matériel introuvable.');
            $this->redirect('/admin/tools');
        }

        $material = $this->materials->find($id);
        if (!$material) {
            $this->session->flash('error', 'Matériel introuvable.');
            $this->redirect('/admin/tools');
        }

        if ($error = $this->checkUploadLimits()) {
            $this->session->flash('error', $error);
            $this->redirect('/admin/materials/edit?id=' . urlencode($id));
        }

        $quantityTotal = (int) ($_POST['quantity_total'] ?? $material['quantity_total']);

        try {
            $images = $this->processMaterialImages([
                'cover' => $material['cover_image'] ?? null,
                'gallery' => $material['gallery'] ?? [],
            ]);
            $trackingMode = $_POST['tracking_mode'] ?? ($material['tracking_mode'] ?? 'generic');
            $identifiers = $this->parseIdentifiers($_POST['identifiers'] ?? ($material['identifiers'] ?? []));
            $replacementCost = $_POST['replacement_cost'] ?? ($material['replacement_cost'] ?? null);

            if ($replacementCost !== null && $replacementCost !== '') {
                $replacementCost = (float) $replacementCost;
            } else {
                $replacementCost = null;
            }

            if ($trackingMode === 'numbered') {
                $identifiers = $this->generateIdentifiers($id, $quantityTotal, $identifiers);
            } else {
                $identifiers = [];
            }

            $this->materials->update($id, [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'quantity_total' => $quantityTotal,
                'cover_image' => $images['cover'],
                'gallery' => $images['gallery'],
                'tracking_mode' => $trackingMode,
                'identifiers' => $identifiers,
                'replacement_cost' => $replacementCost,
            ]);
        } catch (\InvalidArgumentException $e) {
            $this->session->flash('error', $e->getMessage());
            $this->redirect('/admin/materials/edit?id=' . urlencode($id));
            return;
        } catch (\RuntimeException $e) {
            $this->session->flash('error', $e->getMessage());
            $this->redirect('/admin/materials/edit?id=' . urlencode($id));
            return;
        }

        $this->session->flash('success', 'Matériel mis à jour.');
        $this->redirect('/admin/tools');
    }

    public function storeUser(): void
    {
        $current = $this->requireAuth(['admin', 'responsable']);

        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $status = $_POST['status'] ?? 'pending';
        $roles = $_POST['roles'] ?? [];

        if ($name === '' || $email === '' || $password === '') {
            $this->session->flash('error', 'Nom, email et mot de passe sont obligatoires.');
            $this->redirect('/admin/users');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->flash('error', 'Adresse email invalide.');
            $this->redirect('/admin/users');
        }

        $allowedDomain = config('auth.allowed_domain', '');
        if ($allowedDomain && !str_ends_with($email, $allowedDomain)) {
            $this->session->flash('error', 'L’adresse email doit se terminer par ' . $allowedDomain . '.');
            $this->redirect('/admin/users');
        }

        $roles = array_values(array_filter(
            array_map('strtolower', (array) $roles),
            static fn($role) => $role !== ''
        ));

        if (empty($roles)) {
            $roles = ['etudiant'];
        }

        // Only admins can assign admin role
        if (in_array('admin', $roles) && !in_array('admin', $current['roles'] ?? [])) {
            $this->session->flash('error', 'Seul un administrateur peut attribuer le rôle d\'administrateur.');
            $this->redirect('/admin/users');
        }

        $allowedStatuses = $this->users->availableStatuses();
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'pending';
        }

        try {
            $this->users->create([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'status' => $status,
            ], $roles);
        } catch (\InvalidArgumentException $e) {
            $this->session->flash('error', $e->getMessage());
            $this->redirect('/admin/users');
            return;
        } catch (\RuntimeException $e) {
            $this->session->flash('error', $e->getMessage());
            $this->redirect('/admin/users');
            return;
        }

        $this->session->flash('success', 'Utilisateur créé avec succès.');
        $this->redirect('/admin/users');
    }

    public function updateUser(): void
    {
        $current = $this->requireAuth(['admin', 'responsable']);

        $userId = $_POST['user_id'] ?? null;
        if (!$userId) {
            $this->session->flash('error', 'Utilisateur introuvable.');
            $this->redirect('/admin/users');
        }

        $target = $this->users->findById($userId);
        if (!$target) {
            $this->session->flash('error', 'Utilisateur introuvable.');
            $this->redirect('/admin/users');
        }

        $roles = array_values(array_filter(
            array_map('strtolower', (array) ($_POST['roles'] ?? [])),
            static fn($role) => $role !== ''
        ));

        if (empty($roles)) {
            $roles = ['etudiant'];
        }

        // Only admins can assign admin role
        if (in_array('admin', $roles) && !in_array('admin', $current['roles'] ?? [])) {
            $this->session->flash('error', 'Seul un administrateur peut attribuer le rôle d\'administrateur.');
            $this->redirect('/admin/users');
        }

        // Prevent removing admin role from the last admin
        $currentRoles = $target['roles'] ?? [];
        if (in_array('admin', $currentRoles) && !in_array('admin', $roles)) {
            if (!in_array('admin', $current['roles'] ?? [])) {
                $this->session->flash('error', 'Seul un administrateur peut retirer le rôle d\'administrateur.');
                $this->redirect('/admin/users');
            }
        }

        $status = $_POST['status'] ?? 'pending';
        $allowedStatuses = $this->users->availableStatuses();
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'pending';
        }

        try {
            $this->users->updateRoles($userId, $roles);
            $this->users->updateStatus($userId, $status);
            $this->users->ensureAdminExists();
        } catch (\InvalidArgumentException $e) {
            $this->session->flash('error', $e->getMessage());
            $this->redirect('/admin/users');
            return;
        }

        $this->session->flash('success', 'Utilisateur mis à jour.');
        $this->redirect('/admin/users');
    }

    public function deleteUser(): void
    {
        $current = $this->requireAuth(['admin', 'responsable']);

        $userId = $_POST['user_id'] ?? null;
        if (!$userId) {
            $this->session->flash('error', 'Utilisateur introuvable.');
            $this->redirect('/admin/users');
        }

        if ($userId === ($current['id'] ?? '')) {
            $this->session->flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            $this->redirect('/admin/users');
        }

        $target = $this->users->findById($userId);
        if (!$target) {
            $this->session->flash('error', 'Utilisateur introuvable.');
            $this->redirect('/admin/users');
        }

        $this->users->delete($userId);
        $this->users->ensureAdminExists();

        $this->session->flash('success', 'Utilisateur supprimé.');
        $this->redirect('/admin/users');
    }

    public function addWhitelist(): void
    {
        $this->requireAuth(['admin', 'responsable']);

        $email = strtolower(trim($_POST['email'] ?? ''));
        $startsAt = $_POST['starts_at'] ?? null;
        $endsAt = $_POST['ends_at'] ?? null;
        $startsAt = $startsAt !== '' ? $startsAt : null;
        $endsAt = $endsAt !== '' ? $endsAt : null;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->flash('error', 'Adresse email invalide.');
            $this->redirect('/admin/users');
        }

        try {
            $this->users->addToWhitelist($email, $startsAt, $endsAt);
        } catch (\InvalidArgumentException $e) {
            $this->session->flash('error', $e->getMessage());
            $this->redirect('/admin/users');
        }

        $this->session->flash('success', 'Adresse ajoutée à la liste blanche.');
        $this->redirect('/admin/users');
    }

    public function removeWhitelist(): void
    {
        $this->requireAuth(['admin', 'responsable']);

        $email = strtolower(trim($_POST['email'] ?? ''));
        if ($email === '') {
            $this->session->flash('error', 'Adresse email introuvable.');
            $this->redirect('/admin/users');
        }

        $this->users->removeFromWhitelist($email);
        $this->session->flash('success', 'Adresse retirée de la liste blanche.');
        $this->redirect('/admin/users');
    }

    public function importWhitelist(): void
    {
        $this->requireAuth(['admin', 'responsable']);

        $file = $_FILES['whitelist_csv'] ?? null;
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            $this->session->flash('error', 'Aucun fichier CSV fourni.');
            $this->redirect('/admin/users');
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $this->session->flash('error', 'Erreur lors du téléversement du fichier CSV.');
            $this->redirect('/admin/users');
        }

        $defaultStart = $_POST['default_starts_at'] ?? null;
        $defaultEnd = $_POST['default_ends_at'] ?? null;
        $defaultStart = $defaultStart !== '' ? $defaultStart : null;
        $defaultEnd = $defaultEnd !== '' ? $defaultEnd : null;

        try {
            $summary = $this->users->importWhitelistFromCsv($file['tmp_name'], $defaultStart, $defaultEnd);
        } catch (\Throwable $e) {
            $this->session->flash('error', $e->getMessage());
            $this->redirect('/admin/users');
        }

        $message = sprintf(
            'Import CSV terminé : %d ajouté(s), %d mis(e)(s) à jour, %d ignoré(s).',
            $summary['imported'],
            $summary['updated'],
            $summary['skipped']
        );

        $this->session->flash('success', $message);

        if (!empty($summary['errors'])) {
            $preview = array_slice($summary['errors'], 0, 5);
            $details = implode(' | ', $preview);
            if (count($summary['errors']) > 5) {
                $details .= sprintf(' | ... (%d erreur(s) supplémentaire(s))', count($summary['errors']) - 5);
            }
            $this->session->flash('error', $details);
        }

        $this->redirect('/admin/users');
    }

    public function updateReservation(): void
    {
        $this->requireAuth(['admin', 'responsable']);

        $reservationId = $_POST['reservation_id'] ?? null;
        if (!$reservationId) {
            $this->session->flash('error', 'Réservation introuvable.');
            $this->redirect('/admin/reservations');
        }

        $reservation = $this->reservations->find($reservationId);
        if (!$reservation) {
            $this->session->flash('error', 'Réservation introuvable.');
            $this->redirect('/admin/reservations');
        }

        $startDate = $_POST['start_date'] ?? ($reservation['start_date'] ?? null);
        $endDate = $_POST['end_date'] ?? ($reservation['end_date'] ?? null);
        $status = strtolower(trim($_POST['status'] ?? $reservation['status']));
        $comment = trim($_POST['comment'] ?? ($reservation['comment'] ?? ''));

        if ($startDate && $endDate && $startDate > $endDate) {
            $this->session->flash('error', 'La date de retour doit être postérieure à la date de retrait.');
            $this->redirect('/admin/reservations');
        }

        $allowedStatuses = $this->reservations->allowedStatuses();
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'pending';
        }

        $existingItemsMap = [];
        foreach ($reservation['items'] as $item) {
            $existingItemsMap[$item['material_id']] = $item;
        }

        $inputItems = $_POST['items'] ?? null;

        $newItems = [];
        if (is_array($inputItems) && !empty($inputItems)) {
            foreach ($inputItems as $key => $entry) {
                $materialId = strtoupper(trim($entry['material_id'] ?? (is_string($key) ? $key : '')));
                if ($materialId === '') {
                    continue;
                }

                $quantity = array_key_exists('quantity', $entry)
                    ? max(0, (int) $entry['quantity'])
                    : (int) ($existingItemsMap[$materialId]['quantity'] ?? 0);

                if ($quantity < 1) {
                    continue;
                }

                $identifiersInput = $entry['identifiers'] ?? ($existingItemsMap[$materialId]['identifiers'] ?? []);
                $identifiers = $this->parseIdentifiers($identifiersInput);

                // Parse damaged identifiers
                $damagedIdentifiersInput = $entry['damaged_identifiers'] ?? [];
                $damagedIdentifiers = $this->parseIdentifiers($damagedIdentifiersInput);

                $newItems[$materialId] = [
                    'material_id' => $materialId,
                    'quantity' => $quantity,
                    'identifiers' => $identifiers,
                    'return_status' => $entry['return_status'] ?? ($existingItemsMap[$materialId]['return_status'] ?? 'ok'),
                    'return_condition' => $entry['return_condition'] ?? ($existingItemsMap[$materialId]['return_condition'] ?? null),
                    'damaged_identifiers' => $damagedIdentifiers,
                ];
            }
        }

        if (empty($newItems)) {
            foreach ($existingItemsMap as $materialId => $item) {
                $newItems[$materialId] = [
                    'material_id' => $materialId,
                    'quantity' => (int) ($item['quantity'] ?? 0),
                    'identifiers' => $this->parseIdentifiers($item['identifiers'] ?? []),
                    'return_status' => $item['return_status'] ?? 'ok',
                    'return_condition' => $item['return_condition'] ?? null,
                ];
            }
        }

        if (empty($newItems)) {
            $this->session->flash('error', 'La réservation doit contenir au moins un matériel.');
            $this->redirect('/admin/reservations');
        }

        $materialsCache = [];
        foreach (array_keys($newItems) as $materialId) {
            $materialData = $this->materials->find($materialId);
            if (!$materialData) {
                $this->session->flash('error', "Matériel {$materialId} introuvable.");
                $this->redirect('/admin/reservations');
            }
            $materialsCache[$materialId] = $materialData;
        }

        if (!$startDate || !$endDate) {
            $this->session->flash('error', 'Les dates de réservation sont obligatoires.');
            $this->redirect('/admin/reservations');
        }

        foreach ($newItems as $materialId => $itemData) {
            $quantity = $itemData['quantity'];
            $available = $this->materials->availabilityForPeriod($materialId, $startDate, $endDate, $reservationId);
            if ($available < $quantity) {
                $name = $materialsCache[$materialId]['name'] ?? $materialId;
                $message = $available > 0
                    ? "Stock insuffisant pour {$name} sur cette période. Disponibles : {$available}."
                    : "Le matériel {$name} est déjà entièrement réservé sur cette période.";
                $this->session->flash('error', $message);
                $this->redirect('/admin/reservations');
            }

            $material = $materialsCache[$materialId];
            if (($material['tracking_mode'] ?? 'generic') === 'numbered') {
                $allIdentifiers = $material['identifiers'] ?? [];
                $identifiers = $itemData['identifiers'];

                if (count($identifiers) !== $quantity) {
                    $this->session->flash('error', "Merci de sélectionner {$quantity} identifiant(s) pour {$material['name']}.");
                    $this->redirect('/admin/reservations');
                }

                $invalid = array_diff($identifiers, $allIdentifiers);
                if (!empty($invalid)) {
                    $this->session->flash('error', 'Identifiants inconnus pour ' . $material['name'] . '.');
                    $this->redirect('/admin/reservations');
                }

                $availableIdentifiers = $this->materials->availableIdentifiers($materialId, $startDate, $endDate, $reservationId);

                $conflicts = array_diff($identifiers, $availableIdentifiers);
                if (!empty($conflicts)) {
                    $this->session->flash('error', 'Certains identifiants sont déjà attribués sur cette période pour ' . $material['name'] . '.');
                    $this->redirect('/admin/reservations');
                }
            }
        }

        $itemsPayload = [];
        foreach ($newItems as $materialId => $data) {
            $itemsPayload[] = [
                'material_id' => $materialId,
                'quantity' => $data['quantity'],
                'identifiers' => $data['identifiers'],
                'return_status' => $data['return_status'] ?? 'ok',
                'return_condition' => $data['return_condition'] ?? null,
                'damaged_identifiers' => $data['damaged_identifiers'] ?? [], // Nouveaux identifiants endommagés
            ];
        }

        // Update reservation
        $this->reservations->update($reservationId, [
            'items' => $itemsPayload,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
            'comment' => $comment,
        ]);

        // If status is "returned", update material availability based on damaged items
        if ($status === 'returned') {
            $this->processDamagedItems($reservationId, $itemsPayload);
        }

        $this->session->flash('success', 'Réservation mise à jour.');
        $this->redirect('/admin/reservations');
    }

    public function deleteReservation(): void
    {
        $user = $this->requireAuth(['admin', 'responsable']);

        $reservationId = $_POST['reservation_id'] ?? null;
        if (!$reservationId) {
            $this->session->flash('error', 'Réservation introuvable.');
            $this->redirect('/admin/reservations');
        }

        $reservation = $this->reservations->find($reservationId);
        if (!$reservation) {
            $this->session->flash('error', 'Réservation introuvable.');
            $this->redirect('/admin/reservations');
        }

        $deleted = $this->reservations->delete($reservationId);
        if ($deleted) {
            $this->trash->store('reservation', $reservationId, $deleted, [
                'user_id' => $user['id'] ?? null,
                'user_name' => $user['name'] ?? null,
                'user_role' => $user['roles'][0] ?? null,
            ]);
            $this->session->flash('success', 'Réservation déplacée dans la corbeille.');
        } else {
            $this->session->flash('error', 'Impossible de supprimer cette réservation.');
        }
        $this->redirect('/admin/reservations');
    }

    public function restoreTrash(): void
    {
        $this->requireAuth(['admin']);

        $trashId = $_POST['trash_id'] ?? null;
        if (!$trashId) {
            $this->session->flash('error', 'Élément introuvable.');
            $this->redirect('/admin/trash');
        }

        $entry = $this->trash->remove($trashId);
        if (!$entry) {
            $this->session->flash('error', 'Élément déjà restauré ou inexistant.');
            $this->redirect('/admin/trash');
        }

        $type = $entry['type'] ?? null;
        $payload = $entry['payload'] ?? [];
        switch ($type) {
            case 'reservation':
                $restored = $this->reservations->restore($payload);
                if ($restored) {
                    $this->session->flash('success', 'Réservation restaurée.');
                } else {
                    $this->session->flash('error', 'Impossible de restaurer la réservation.');
                }
                break;
            default:
                $this->session->flash('error', 'Type non pris en charge pour la restauration.');
        }

        $this->redirect('/admin/trash');
    }

    public function destroyTrash(): void
    {
        $this->requireAuth(['admin']);

        $trashId = $_POST['trash_id'] ?? null;
        if (!$trashId) {
            $this->session->flash('error', 'Élément introuvable.');
            $this->redirect('/admin/trash');
        }

        $entry = $this->trash->remove($trashId);
        if (!$entry) {
            $this->session->flash('error', 'Élément déjà supprimé.');
        } else {
            $this->session->flash('success', 'Élément supprimé définitivement.');
        }

        $this->redirect('/admin/trash');
    }

    public function previewEmail(): void
    {
        $this->requireAuth(['admin', 'responsable']);

        $template = $_POST['template'] ?? 'reservation_confirmation';

        $sample = $this->notifications->buildReservationEmail($template, [
            'user_name' => 'Jean Dupont',
            'material_name' => 'Caméra 4K',
            'start_date' => '2025-01-10',
            'end_date' => '2025-01-12',
        ]);

        $this->session->flash(
            'success',
            "Aperçu généré. Sujet : {$sample['subject']}"
        );

        $this->redirect('/admin/tools?preview=' . urlencode($template));
    }

    private function processMaterialImages(array $current = []): array
    {
        $entries = [];
        $allowedExisting = [];

        if (!empty($current['cover'])) {
            $allowedExisting[$current['cover']] = true;
        }

        foreach ($current['gallery'] ?? [] as $path) {
            $allowedExisting[$path] = true;
        }

        $existingInputs = $_POST['existing_images'] ?? [];
        if (!is_array($existingInputs)) {
            $existingInputs = [$existingInputs];
        }

        foreach ($existingInputs as $path) {
            $path = trim((string) $path);
            if ($path === '') {
                continue;
            }

            if ($allowedExisting && !isset($allowedExisting[$path])) {
                continue;
            }

            $entries[] = [
                'path' => $path,
                'identifier' => 'existing::' . base64_encode($path),
            ];
        }

        $files = $_FILES['material_images'] ?? null;
        if ($files && is_array($files['name'])) {
            $count = count($files['name']);

            for ($i = 0; $i < $count; $i++) {
                $file = [
                    'name' => $files['name'][$i] ?? '',
                    'type' => $files['type'][$i] ?? '',
                    'tmp_name' => $files['tmp_name'][$i] ?? '',
                    'error' => $files['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                    'size' => $files['size'][$i] ?? 0,
                ];

                if ($file['error'] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    throw new \RuntimeException('Erreur lors du téléversement d’une image.');
                }

                $path = $this->storeUploadedFile($file);
                $entries[] = [
                    'path' => $path,
                    'identifier' => 'new::' . $i,
                ];
            }
        }

        $urlEntries = [];

        $remoteInputs = $_POST['remote_images'] ?? [];
        if (!is_array($remoteInputs)) {
            $remoteInputs = [$remoteInputs];
        }

        foreach ($remoteInputs as $url) {
            $url = trim((string) $url);
            if ($url !== '') {
                $urlEntries[] = $url;
            }
        }

        $urlsRaw = trim($_POST['material_images_urls'] ?? ($_POST['gallery_urls'] ?? ''));
        if ($urlsRaw !== '') {
            $urls = preg_split('/\r\n|\n|\r/', $urlsRaw);
            foreach ($urls as $url) {
                $url = trim((string) $url);
                if ($url !== '') {
                    $urlEntries[] = $url;
                }
            }
        }

        foreach ($urlEntries as $index => $url) {
            $entries[] = [
                'path' => $url,
                'identifier' => 'url::' . $index,
            ];
        }

        $coverChoice = $_POST['cover_choice'] ?? '';
        $cover = null;
        $gallery = [];

        foreach ($entries as $entry) {
            if ($coverChoice !== '' && $entry['identifier'] === $coverChoice) {
                $cover = $entry;
                continue;
            }

            if ($cover === null && $coverChoice === '') {
                $cover = $entry;
                continue;
            }

            $gallery[] = $entry;
        }

        if ($cover === null && !empty($gallery)) {
            $cover = array_shift($gallery);
        }

        $coverPath = $cover['path'] ?? null;
        $galleryPaths = array_map(fn($entry) => $entry['path'], $gallery);
        $galleryPaths = array_values(array_unique($galleryPaths));

        return [
            'cover' => $coverPath,
            'gallery' => $galleryPaths,
        ];
    }

    private function storeUploadedFile(array $file): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Téléversement invalide.');
        }

        if (($file['size'] ?? 0) > $this->maxUploadSize) {
            throw new \InvalidArgumentException('Une image dépasse la taille maximale autorisée de 5 Mo.');
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            throw new \InvalidArgumentException('Format d’image non supporté. Formats autorisés : ' . implode(', ', $allowedExtensions) . '.');
        }

        $targetDir = __DIR__ . '/../../web/uploads/materials';

        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
                throw new \RuntimeException('Impossible de créer le dossier de stockage des images.');
            }
        }

        $filename = uniqid('mat_', true) . '.' . $extension;
        $targetPath = $targetDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \RuntimeException('Impossible d’enregistrer le fichier téléchargé.');
        }

        return 'uploads/materials/' . $filename;
    }

    private function checkUploadLimits(): ?string
    {
        $limitMb = round($this->maxUploadSize / (1024 * 1024));

        if (!empty($_FILES['material_images']) && is_array($_FILES['material_images']['name'])) {
            $files = $_FILES['material_images'];
            $count = count($files['name']);

            for ($i = 0; $i < $count; $i++) {
                $error = $files['error'][$i] ?? UPLOAD_ERR_NO_FILE;
                $size = $files['size'][$i] ?? 0;

                if (in_array($error, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
                    return "Une image dépasse la taille maximale autorisée ({$limitMb} Mo).";
                }

                if ($error === UPLOAD_ERR_OK && $size > $this->maxUploadSize) {
                    return "Une image dépasse la taille maximale autorisée ({$limitMb} Mo).";
                }
            }
        }

        return null;
    }

    /**
     * @param mixed $input
     * @return string[]
     */
    private function parseIdentifiers(mixed $input): array
    {
        if (is_array($input)) {
            $raw = $input;
        } else {
            $raw = preg_split('/\r\n|\n|\r|,/', (string) $input) ?: [];
        }

        return array_values(array_unique(array_filter(array_map(
            static fn($value) => is_string($value) ? trim($value) : '',
            $raw
        ))));
    }

    /**
     * @param string $baseId
     * @param int $quantity
     * @param array<int,string> $existing
     * @return array<int,string>
     */
    private function generateIdentifiers(string $baseId, int $quantity, array $existing = []): array
    {
        $base = trim($baseId);
        if ($base === '') {
            $base = 'ITEM';
        }

        $identifiers = array_values(array_unique(array_filter(array_map(
            static fn($value) => is_string($value) ? trim($value) : '',
            $existing
        ))));

        // Ensure existing count does not exceed quantity
        if (count($identifiers) > $quantity) {
            $identifiers = array_slice($identifiers, 0, $quantity);
        }

        $used = array_flip(array_map('strtoupper', $identifiers));
        $counter = 1;
        while (count($identifiers) < $quantity) {
            $candidate = sprintf('%s-%03d', $base, $counter);
            if (!isset($used[strtoupper($candidate)])) {
                $identifiers[] = $candidate;
                $used[strtoupper($candidate)] = true;
            }
            $counter++;
        }

        return $identifiers;
    }

    /**
     * Process damaged items and update material availability
     * For numbered tracking: mark specific identifiers as damaged in materials table
     * For generic tracking: reduce available quantity based on damaged count
     * 
     * IMPORTANT: Broken/Lost items are REMOVED from the reservation
     *            Damaged items stay in the reservation
     * 
     * @param string $reservationId
     * @param array $items
     * @return void
     */
    private function processDamagedItems(string $reservationId, array $items): void
    {
        $updatedItems = [];
        $hasChanges = false;

        foreach ($items as $item) {
            $materialId = $item['material_id'];
            $returnStatus = $item['return_status'] ?? 'ok';
            $damagedIdentifiers = $item['damaged_identifiers'] ?? [];
            $allIdentifiers = $item['identifiers'] ?? [];
            $quantity = $item['quantity'] ?? 0;

            $material = $this->materials->find($materialId);
            if (!$material) {
                $updatedItems[] = $item;
                continue;
            }

            $trackingMode = $material['tracking_mode'] ?? 'generic';
            $currentDamagedItems = $material['damaged_items'] ?? [];
            $newDamagedItems = $currentDamagedItems;

            $permanentlyLostIdentifiers = []; // For numbered items
            $permanentlyLostQuantity = 0;     // For generic items

            if ($trackingMode === 'numbered') {
                // --- NUMBERED TRACKING ---

                if ($returnStatus === 'broken' || $returnStatus === 'lost') {
                    // ALL identifiers are broken/lost → Remove ALL from reservation
                    $permanentlyLostIdentifiers = $allIdentifiers;

                    foreach ($allIdentifiers as $identifier) {
                        if (!isset($newDamagedItems[$identifier])) {
                            $newDamagedItems[$identifier] = [
                                'status' => $returnStatus,
                                'condition' => $item['return_condition'] ?? null,
                                'reported_at' => date('Y-m-d H:i:s'),
                                'reservation_id' => $reservationId,
                            ];
                        }
                    }
                } elseif ($returnStatus === 'damaged' && !empty($damagedIdentifiers)) {
                    // SOME identifiers are damaged → Check if broken/lost or just damaged
                    // For now, we assume "damaged" means repairable, so they stay in reservation
                    // But we mark them in the material's damaged_items

                    foreach ($damagedIdentifiers as $identifier) {
                        if (in_array($identifier, $allIdentifiers) && !isset($newDamagedItems[$identifier])) {
                            $newDamagedItems[$identifier] = [
                                'status' => 'damaged',
                                'condition' => $item['return_condition'] ?? null,
                                'reported_at' => date('Y-m-d H:i:s'),
                                'reservation_id' => $reservationId,
                            ];
                        }
                    }

                    // Don't remove damaged items from reservation (they're repairable)
                }

                // Update the item - remove broken/lost identifiers from reservation
                if (!empty($permanentlyLostIdentifiers)) {
                    $remainingIdentifiers = array_values(array_diff($allIdentifiers, $permanentlyLostIdentifiers));

                    if (!empty($remainingIdentifiers)) {
                        // Some items remain
                        $item['identifiers'] = $remainingIdentifiers;
                        $item['quantity'] = count($remainingIdentifiers);
                        $updatedItems[] = $item;
                    }
                    // If all lost → don't add to updatedItems (removes entire item from reservation)

                    $hasChanges = true;
                } else {
                    $updatedItems[] = $item;
                }

            } else {
                // --- GENERIC TRACKING ---

                if ($returnStatus === 'broken' || $returnStatus === 'lost') {
                    // ALL quantity is broken/lost → Remove from reservation
                    $permanentlyLostQuantity = $quantity;

                    $damageKey = uniqid('damage_');
                    $newDamagedItems[$damageKey] = [
                        'quantity' => $quantity,
                        'status' => $returnStatus,
                        'condition' => $item['return_condition'] ?? null,
                        'reported_at' => date('Y-m-d H:i:s'),
                        'reservation_id' => $reservationId,
                    ];

                    // Don't add to updatedItems → removes from reservation
                    $hasChanges = true;

                } elseif ($returnStatus === 'damaged') {
                    // Damaged but repairable → stays in reservation
                    $damageKey = uniqid('damage_');
                    $newDamagedItems[$damageKey] = [
                        'quantity' => $quantity,
                        'status' => 'damaged',
                        'condition' => $item['return_condition'] ?? null,
                        'reported_at' => date('Y-m-d H:i:s'),
                        'reservation_id' => $reservationId,
                    ];

                    $updatedItems[] = $item;
                } else {
                    $updatedItems[] = $item;
                }
            }

            // Update material with damaged items info
            try {
                $this->materials->update($materialId, [
                    'damaged_items' => $newDamagedItems,
                ]);

                // Reduce total and available quantity for permanently lost items
                $permanentlyLost = $trackingMode === 'numbered'
                    ? count($permanentlyLostIdentifiers)
                    : $permanentlyLostQuantity;

                if ($permanentlyLost > 0) {
                    $newTotal = max(0, $material['quantity_total'] - $permanentlyLost);
                    $newAvailable = max(0, $material['quantity_available'] - $permanentlyLost);

                    $this->materials->update($materialId, [
                        'quantity_total' => $newTotal,
                        'quantity_available' => $newAvailable,
                    ]);
                }
            } catch (\Exception $e) {
                error_log("Failed to update damaged items for material {$materialId}: " . $e->getMessage());
            }
        }

        // If items were removed, update the reservation
        if ($hasChanges && !empty($updatedItems)) {
            try {
                // Delete all items and re-insert the updated ones
                $this->connection->prepare('DELETE FROM reservation_items WHERE reservation_id = :id')
                    ->execute(['id' => $reservationId]);

                // Use a helper to insert items
                foreach ($updatedItems as $item) {
                    $this->reservations->insertReservationItem($reservationId, $item);
                }
            } catch (\Exception $e) {
                error_log("Failed to update reservation items after damage processing: " . $e->getMessage());
            }
        } elseif ($hasChanges && empty($updatedItems)) {
            // All items were removed - this shouldn't happen in practice, but handle it
            error_log("All items removed from reservation {$reservationId} due to damage");
        }
    }
}
