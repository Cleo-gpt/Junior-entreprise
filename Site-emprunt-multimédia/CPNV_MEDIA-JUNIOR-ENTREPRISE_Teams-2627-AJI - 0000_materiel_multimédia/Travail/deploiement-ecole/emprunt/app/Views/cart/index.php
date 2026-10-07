<?php
$hasItems = !empty($items);
$blockedDates = $blockedDates ?? [];
$defaultStartDate = $defaultStartDate ?? null;
$defaultEndDate = $defaultEndDate ?? $defaultStartDate;
$horizonDate = $horizonDate ?? (new DateTimeImmutable('today +6 months'))->format('Y-m-d');
$blockedDatesJson = htmlspecialchars(json_encode($blockedDates), ENT_QUOTES);
$hasAvailableDates = !$hasItems || $defaultStartDate !== null;
?>

<div class="d-flex flex-column flex-lg-row gap-4">
    <div class="flex-grow-1">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Mon panier</h1>
                <p class="text-muted mb-0">Réservez plusieurs matériels en une seule demande.</p>
            </div>
            <a href="<?= route('/materials'); ?>" class="btn btn-outline-secondary">Continuer la sélection</a>
        </div>

        <?php if (!$hasItems): ?>
            <div class="empty-state">
                <img src="<?= asset('img/empty-box.svg'); ?>" alt="Panier vide">
                <h2 class="h5 mt-3">Votre panier est vide</h2>
                <p class="text-muted mb-3">Ajoutez du matériel depuis le catalogue pour créer une réservation groupée.</p>
                <a class="btn btn-outline-primary" href="<?= route('/materials'); ?>">Voir le catalogue</a>
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Matériel</th>
                                    <th class="text-center">Stock total</th>
                                    <th class="text-center">Quantité</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $entry):
                                    $material = $entry['material'];
                                    $quantity = $entry['quantity'];
                                    $stockTotal = (int) ($material['quantity_total'] ?? 0);
                                ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?= htmlspecialchars($material['name']); ?></div>
                                            <div class="text-muted small"><?= htmlspecialchars($material['description']); ?></div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">
                                                <?= $stockTotal; ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <form action="<?= route('/cart/update'); ?>" method="POST" class="d-inline-flex align-items-center gap-2 justify-content-center">
                                                <input type="hidden" name="material_id" value="<?= htmlspecialchars($material['id']); ?>">
                                                <input type="number" class="form-control form-control-sm cart-qty-input"
                                                       name="quantity"
                                                       value="<?= (int) $quantity; ?>"
                                                       min="0"
                                                       max="<?= $stockTotal; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-primary">Mettre à jour</button>
                                            </form>
                                        </td>
                                        <td class="text-end">
                                            <form action="<?= route('/cart/remove'); ?>" method="POST" class="d-inline">
                                                <input type="hidden" name="material_id" value="<?= htmlspecialchars($material['id']); ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Retirer</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <form action="<?= route('/cart/clear'); ?>" method="POST" class="mt-3">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Vider le panier</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="cart-summary card shadow-sm">
        <div class="card-body">
            <h2 class="h5 mb-3">Valider la réservation</h2>
            <?php if (!$hasItems): ?>
                <p class="text-muted mb-0">Ajoutez du matériel avant de valider votre réservation.</p>
            <?php elseif (!$hasAvailableDates): ?>
                <div class="alert alert-warning mb-0" role="alert">
                    Aucune date n’est disponible pour les matériels de votre panier. Merci de contacter un responsable.
                </div>
            <?php else: ?>
                <form action="<?= route('/cart/checkout'); ?>" method="POST" class="row g-3">
                    <div class="col-12">
                        <label for="start_date" class="form-label">Date de retrait</label>
                        <input
                            type="date"
                            class="form-control"
                            id="start_date"
                            name="start_date"
                            required
                            min="<?= htmlspecialchars((new DateTimeImmutable('today'))->format('Y-m-d')); ?>"
                            max="<?= htmlspecialchars($horizonDate); ?>"
                            value="<?= htmlspecialchars($defaultStartDate); ?>"
                            data-blocked-dates="<?= $blockedDatesJson; ?>"
                            data-horizon="<?= htmlspecialchars($horizonDate); ?>"
                        >
                    </div>
                    <div class="col-12">
                        <label for="end_date" class="form-label">Date de retour</label>
                        <input
                            type="date"
                            class="form-control"
                            id="end_date"
                            name="end_date"
                            required
                            min="<?= htmlspecialchars($defaultStartDate); ?>"
                            max="<?= htmlspecialchars($horizonDate); ?>"
                            value="<?= htmlspecialchars($defaultEndDate); ?>"
                            data-default="<?= htmlspecialchars($defaultEndDate ?? ''); ?>"
                        >
                    </div>
                    <div class="col-12">
                        <label for="comment" class="form-label">Commentaire (optionnel)</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Notes spécifiques, contraintes horaires, etc."></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100">Confirmer la réservation</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

