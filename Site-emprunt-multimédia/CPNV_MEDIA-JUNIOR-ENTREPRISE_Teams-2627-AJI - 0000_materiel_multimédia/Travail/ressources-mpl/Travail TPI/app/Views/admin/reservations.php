<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Gestion des réservations</h1>
        <p class="text-muted mb-0">Validez les retraits/retours et ajustez les réservations des utilisateurs.</p>
    </div>
    <div class="d-flex gap-2">
        <?php if (!empty($user['roles']) && in_array('admin', $user['roles'], true)): ?>
            <a href="<?= route('/admin/trash'); ?>" class="btn btn-outline-secondary">Corbeille</a>
        <?php endif; ?>
        <a href="<?= route('/admin/incidents'); ?>" class="btn btn-outline-danger">Incidents</a>
        <a href="<?= route('/admin/tools'); ?>" class="btn btn-outline-secondary">Gestion du matériel</a>
        <a href="<?= route('/admin/users'); ?>" class="btn btn-outline-secondary">Gestion des utilisateurs</a>
    </div>
</div>

<form action="<?= route('/admin/reservations'); ?>" method="get" class="row g-3 align-items-end mb-4">
    <div class="col-md-4 col-sm-6">
        <label for="status_filter" class="form-label">Filtrer par statut</label>
        <select class="form-select" id="status_filter" name="status">
            <option value="all" <?= ($statusFilter ?? 'all') === 'all' ? 'selected' : ''; ?>>Tous les statuts</option>
            <?php foreach ($statusLabels as $key => $label): ?>
                <option value="<?= htmlspecialchars($key); ?>" <?= ($statusFilter ?? 'all') === $key ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 col-sm-6 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-grow-1">Appliquer</button>
        <?php if (($statusFilter ?? 'all') !== 'all'): ?>
            <a href="<?= route('/admin/reservations'); ?>" class="btn btn-outline-secondary">Réinitialiser</a>
        <?php endif; ?>
    </div>
</form>

<?php if (empty($reservations)): ?>
    <div class="empty-state">
        <img src="<?= asset('img/empty-box.svg'); ?>" alt="Aucune réservation">
        <h2 class="h5 mt-3">Aucune réservation enregistrée</h2>
        <p class="text-muted mb-0">Les réservations des utilisateurs apparaîtront ici pour validation.</p>
    </div>
<?php else: ?>
    <div class="d-flex flex-column gap-4">
        <?php foreach ($reservations as $reservation): ?>
            <?php
                $userInfo = $users[$reservation['user_id']] ?? null;
                $userName = $userInfo['name'] ?? 'Utilisateur inconnu';
                $userEmail = $userInfo['email'] ?? 'Email inconnu';
                $status = $reservation['status'] ?? 'pending';
                $statusLabel = $statusLabels[$status] ?? ucfirst($status);
                $holdsStock = in_array($status, ['pending', 'approved', 'checked_out'], true);
                $items = $reservation['items'] ?? [];
            ?>
            <div class="card reservation-admin-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <h2 class="h5 mb-1">Réservation <?= htmlspecialchars(substr($reservation['id'], 0, 10)); ?></h2>
                            <p class="text-muted mb-0">
                                Réservé par <?= htmlspecialchars($userName); ?>
                                <span class="text-muted"> (<?= htmlspecialchars($userEmail); ?>)</span>
                            </p>
                        </div>
                        <span class="badge bg-light text-dark">
                            <?= htmlspecialchars($statusLabel); ?>
                        </span>
                    </div>

                    <dl class="row small mb-3">
                        <dt class="col-sm-3">Identifiant réservation</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($reservation['id']); ?></dd>
                        <dt class="col-sm-3">Période</dt>
                        <dd class="col-sm-9">
                            <?= htmlspecialchars($reservation['start_date'] ?? 'N/A'); ?>
                            &rarr;
                            <?= htmlspecialchars($reservation['end_date'] ?? 'N/A'); ?>
                        </dd>
                    </dl>

                    <form action="<?= route('/admin/reservations/update'); ?>" method="POST" class="row g-3 align-items-end">
                        <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($reservation['id']); ?>">

                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Matériel</th>
                                            <th class="text-center">Quantité</th>
                                            <th class="text-center">Disponible</th>
                                            <th>Identifiants</th>
                                            <?php if ($status === 'checked_out' || $status === 'returned'): ?>
                                                <th style="width: 200px;">État retour</th>
                                            <?php endif; ?>
                                            <?php if (in_array($status, ['pending', 'approved'], true)): ?>
                                                <th class="text-end">Actions</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                            <?php
                                                $materialId = $item['material_id'];
                                                $quantity = (int) $item['quantity'];
                                                $material = $materials[$materialId] ?? null;
                                                $name = $material['name'] ?? $materialId;
                                                $available = (int) ($material['quantity_available'] ?? 0);
                                                $identifierMeta = $identifierOptions[$reservation['id']][$materialId] ?? null;
                                                $assignedIdentifiers = $item['identifiers'] ?? [];
                                                $allIdentifiers = $identifierMeta['all'] ?? [];
                                                $availableIdentifiers = $identifierMeta['available'] ?? [];
                                                $returnStatus = $item['return_status'] ?? 'ok';
                                                $returnCondition = $item['return_condition'] ?? '';
                                                $currentDamagedIdentifiers = $item['damaged_identifiers'] ?? [];
                                            ?>
                                            <?php 
                                                $isEditable = in_array($status, ['pending', 'approved'], true);
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold"><?= htmlspecialchars($name); ?></div>
                                                    <div class="text-muted small"><?= htmlspecialchars($materialId); ?></div>
                                                    <input type="hidden" name="items[<?= htmlspecialchars($materialId); ?>][material_id]" value="<?= htmlspecialchars($materialId); ?>">
                                                    <?php if (isset($material['replacement_cost']) && $material['replacement_cost'] !== null): ?>
                                                        <div class="text-muted small">
                                                            Prix indicatif : <?= htmlspecialchars(number_format((float) $material['replacement_cost'], 2, '.', '\'')); ?> CHF
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center" style="width: 120px;">
                                                    <?php 
                                                        $totalStock = (int)($material['quantity_total'] ?? 0);
                                                    ?>
                                                    <?php if ($isEditable): ?>
                                                        <input type="number" 
                                                               name="items[<?= htmlspecialchars($materialId); ?>][quantity]" 
                                                               value="<?= $quantity; ?>" 
                                                               min="1" 
                                                               max="<?= $totalStock; ?>"
                                                               class="form-control form-control-sm text-center"
                                                               onchange="checkQuantity(this, <?= $totalStock; ?>)"
                                                        >
                                                        <div class="form-text x-small text-muted my-0">Max: <?= $totalStock; ?></div>
                                                    <?php else: ?>
                                                        <?= $quantity; ?>
                                                        <input type="hidden" name="items[<?= htmlspecialchars($materialId); ?>][quantity]" value="<?= $quantity; ?>">
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?= $available > 0 ? 'success' : 'secondary'; ?>">
                                                        <?= $available; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($identifierMeta): ?>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <?php foreach ($allIdentifiers as $serial): ?>
                                                                <?php
                                                                    $isAssigned = in_array($serial, $assignedIdentifiers, true);
                                                                    $isAvailable = $isAssigned || in_array($serial, $availableIdentifiers, true);
                                                                    $inputId = 'id_' . md5($reservation['id'] . '_' . $materialId . '_' . $serial);
                                                                ?>
                                                                <div class="form-check form-check-inline">
                                                                    <input
                                                                        class="form-check-input"
                                                                        type="checkbox"
                                                                        id="<?= htmlspecialchars($inputId); ?>"
                                                                        name="items[<?= htmlspecialchars($materialId); ?>][identifiers][]"
                                                                        value="<?= htmlspecialchars($serial); ?>"
                                                                        <?= $isAssigned ? 'checked' : ''; ?>
                                                                        <?= $isAvailable ? '' : 'disabled'; ?>
                                                                    >
                                                                    <label class="form-check-label small" for="<?= htmlspecialchars($inputId); ?>">
                                                                        <?= htmlspecialchars($serial); ?>
                                                                        <?php if (!$isAvailable && !$isAssigned): ?>
                                                                            <span class="text-muted">(occupé)</span>
                                                                        <?php endif; ?>
                                                                    </label>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                        <div class="form-text small">
                                                            Sélectionnez <?= $quantity; ?> identifiant(s).
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted small">Non applicable</span>
                                                    <?php endif; ?>
                                                </td>
                                                <?php if ($status === 'checked_out' || $status === 'returned'): ?>
                                                    <td>
                                                        <div class="d-flex flex-column gap-2">
                                                            <select name="items[<?= htmlspecialchars($materialId); ?>][return_status]" class="form-select form-select-sm return-status-select" data-material-id="<?= htmlspecialchars($materialId); ?>" data-has-identifiers="<?= $identifierMeta ? '1' : '0'; ?>">
                                                                <option value="ok" <?= $returnStatus === 'ok' ? 'selected' : ''; ?>>OK</option>
                                                                <option value="damaged" <?= $returnStatus === 'damaged' ? 'selected' : ''; ?>>Endommagé</option>
                                                                <option value="broken" <?= $returnStatus === 'broken' ? 'selected' : ''; ?>>Cassé</option>
                                                                <option value="lost" <?= $returnStatus === 'lost' ? 'selected' : ''; ?>>Perdu/Volé</option>
                                                            </select>
                                                            
                                                            <!-- Sélection des identifiants endommagés (pour matériel numéroté uniquement) -->
                                                            <?php if ($identifierMeta && !empty($assignedIdentifiers)): ?>
                                                                <div class="damaged-identifiers-selector" style="<?= $returnStatus === 'damaged' ? '' : 'display:none;'; ?>">
                                                                    <label class="form-label small mb-1">Identifiants endommagés :</label>
                                                                    <?php foreach ($assignedIdentifiers as $serial): ?>
                                                                        <?php $damageCheckId = 'damage_' . md5($reservation['id'] . '_' . $materialId . '_' . $serial); ?>
                                                                        <div class="form-check">
                                                                            <input
                                                                                class="form-check-input"
                                                                                type="checkbox"
                                                                                id="<?= htmlspecialchars($damageCheckId); ?>"
                                                                                name="items[<?= htmlspecialchars($materialId); ?>][damaged_identifiers][]"
                                                                                value="<?= htmlspecialchars($serial); ?>"
                                                                                <?= in_array($serial, $currentDamagedIdentifiers) ? 'checked' : ''; ?>
                                                                            >
                                                                            <label class="form-check-label small" for="<?= htmlspecialchars($damageCheckId); ?>">
                                                                                <?= htmlspecialchars($serial); ?>
                                                                            </label>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                    <div class="form-text small">Cochez les identifiants endommagés</div>
                                                                </div>
                                                            <?php endif; ?>
                                                            
                                                            <textarea 
                                                                name="items[<?= htmlspecialchars($materialId); ?>][return_condition]" 
                                                                class="form-control form-control-sm return-condition-textarea" 
                                                                rows="2" 
                                                                placeholder="Description (obligatoire si non OK)"
                                                                style="<?= $returnStatus === 'ok' ? 'display:none;' : ''; ?>"
                                                            ><?= htmlspecialchars($returnCondition); ?></textarea>
                                                        </div>
                                                    </td>
                                                <?php endif; ?>
                                                <?php if ($isEditable): ?>
                                                    <td class="text-end">
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" title="Supprimer ce matériel">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                            </svg>
                                                        </button>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="start_<?= htmlspecialchars($reservation['id']); ?>" class="form-label">Date de retrait</label>
                            <input
                                type="date"
                                class="form-control"
                                id="start_<?= htmlspecialchars($reservation['id']); ?>"
                                name="start_date"
                                value="<?= htmlspecialchars($reservation['start_date'] ?? ''); ?>"
                            >
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="end_<?= htmlspecialchars($reservation['id']); ?>" class="form-label">Date de retour</label>
                            <input
                                type="date"
                                class="form-control"
                                id="end_<?= htmlspecialchars($reservation['id']); ?>"
                                name="end_date"
                                value="<?= htmlspecialchars($reservation['end_date'] ?? ''); ?>"
                            >
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="status_<?= htmlspecialchars($reservation['id']); ?>" class="form-label">Statut</label>
                            <select class="form-select" id="status_<?= htmlspecialchars($reservation['id']); ?>" name="status">
                                <?php foreach ($statuses as $option): ?>
                                    <option value="<?= htmlspecialchars($option); ?>" <?= $option === $status ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($statusLabels[$option] ?? ucfirst($option)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="comment_<?= htmlspecialchars($reservation['id']); ?>" class="form-label">Commentaire interne</label>
                            <textarea
                                class="form-control"
                                id="comment_<?= htmlspecialchars($reservation['id']); ?>"
                                name="comment"
                                rows="2"
                                placeholder="Notes éventuelles (ex: matériel incomplet, retard, etc.)"
                            ><?= htmlspecialchars($reservation['comment'] ?? ''); ?></textarea>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Mettre à jour la réservation</button>
                        </div>
                    </form>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" 
                                class="btn btn-sm btn-outline-success" 
                                onclick="submitMainForm('<?= htmlspecialchars($reservation['id']); ?>', 'checked_out')"
                                <?= $status === 'checked_out' ? 'disabled' : ''; ?>>
                            Valider le retrait
                        </button>
                        
                        <button type="button" 
                                class="btn btn-sm btn-outline-primary" 
                                onclick="submitMainForm('<?= htmlspecialchars($reservation['id']); ?>', 'returned')"
                                <?= $status === 'returned' ? 'disabled' : ''; ?>>
                            Valider le retour
                        </button>

                        <form action="<?= route('/admin/reservations/update'); ?>" method="POST" class="d-inline">
                            <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($reservation['id']); ?>">
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn btn-sm btn-outline-warning" <?= $status === 'cancelled' ? 'disabled' : ''; ?>>
                                Annuler la réservation
                            </button>
                        </form>
                        <form action="<?= route('/admin/reservations/delete'); ?>" method="POST" class="d-inline ms-auto">
                            <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($reservation['id']); ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Déplacer vers la corbeille">
                                Corbeille
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
// Handle return status changes
document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.return-status-select');
    
    console.log('[DEBUG] Found', statusSelects.length, 'return status selects');
    
    statusSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            handleReturnStatusChange(this);
        });
    });
    
    function handleReturnStatusChange(select) {
        const container = select.closest('div');
        const textarea = container.querySelector('.return-condition-textarea');
        const damagedSelector = container.querySelector('.damaged-identifiers-selector');
        const value = select.value;
        
        console.log('[DEBUG] Status changed to:', value);
        console.log('[DEBUG] Has damaged selector:', !!damagedSelector);
        
        // Show/hide description textarea
        if (textarea) {
            if (value === 'ok') {
                textarea.style.display = 'none';
                textarea.required = false;
            } else {
                textarea.style.display = 'block';
            }
        }
        
        // Show/hide damaged identifiers selector (only for "damaged" status with numbered items)
        if (damagedSelector) {
            if (value === 'damaged') {
                console.log('[DEBUG] Showing damaged identifiers selector');
                damagedSelector.style.display = 'block';
            } else {
                console.log('[DEBUG] Hiding damaged identifiers selector');
                damagedSelector.style.display = 'none';
                // Uncheck all checkboxes when hiding
                const checkboxes = damagedSelector.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(cb => cb.checked = false);
            }
        }
    }
    
    // Add form submission logging
    const forms = document.querySelectorAll('form[action*="reservations/update"]');
    console.log('[DEBUG] Found', forms.length, 'reservation update forms');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            console.log('[DEBUG] Form submitting...');
            const formData = new FormData(form);
            
            // Log all form data
            for (let [key, value] of formData.entries()) {
                if (key.includes('damaged_identifiers')) {
                    console.log('[DEBUG] DAMAGED IDENTIFIER FOUND:', key, '=', value);
                }
            }
            
            // Check if there are any damaged_identifiers checkboxes
            const damagedCheckboxes = form.querySelectorAll('input[name*="damaged_identifiers"]:checked');
            console.log('[DEBUG] Checked damaged identifiers:', damagedCheckboxes.length);
            damagedCheckboxes.forEach(function(cb) {
                console.log('[DEBUG] - Checked:', cb.name, '=', cb.value);
            });
        });
    });
});

// Legacy function for backwards compatibility
function toggleReturnCondition(select) {
    handleReturnStatusChange(select);
}

function submitMainForm(reservationId, status) {
    // Find the status select for this reservation
    const statusSelect = document.getElementById('status_' + reservationId);
    if (statusSelect) {
        // Change the status
        statusSelect.value = status;
        
        // Find the form containing this select (the main form)
        const form = statusSelect.closest('form');
        if (form) {
            // Submit the main form which includes all item data
            form.submit();
        }
    }
}

function removeRow(button) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce matériel de la réservation ? Cette action sera validée lors de la mise à jour.')) {
        const row = button.closest('tr');
        row.remove();
    }
}

function checkQuantity(input, maxStock) {
    let qty = parseInt(input.value);
    
    if (isNaN(qty) || qty < 1) {
        alert('La quantité doit être au moins de 1.');
        input.value = 1;
        return;
    }
    
    if (qty > maxStock) {
        alert('La quantité ne peut pas dépasser le stock total (' + maxStock + ').');
        input.value = maxStock;
        return;
    }
}
</script>

<style>
.x-small {
    font-size: 0.75rem;
    line-height: 1.2;
}
</style>
