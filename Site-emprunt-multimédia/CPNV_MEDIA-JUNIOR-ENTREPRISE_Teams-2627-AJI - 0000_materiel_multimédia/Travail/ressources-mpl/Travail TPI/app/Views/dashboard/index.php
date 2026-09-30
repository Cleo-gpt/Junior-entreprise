<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1">Bienvenue, <?= htmlspecialchars($user['name']); ?></h1>
        <p class="text-muted mb-0">Voici vos réservations et actions rapides.</p>
    </div>
    <a href="<?= route('/materials'); ?>" class="btn btn-primary">
        Consulter le catalogue
    </a>
</div>

<?php if (empty($reservations)): ?>
    <div class="empty-state">
        <img src="<?= asset('img/empty-box.svg'); ?>" alt="Aucune réservation">
        <h2 class="h5 mt-3">Aucune réservation pour le moment</h2>
        <p class="text-muted mb-3">Réservez facilement du matériel en consultant le catalogue.</p>
        <a class="btn btn-outline-primary" href="<?= route('/materials'); ?>">Voir le matériel disponible</a>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($reservations as $reservation): ?>
            <?php
                $statusLabels = [
                    'pending' => 'En attente',
                    'approved' => 'Validée',
                    'checked_out' => 'Retirée',
                    'returned' => 'Restituée',
                    'cancelled' => 'Annulée',
                ];
                $status = $reservation['status'] ?? 'pending';
                $statusLabel = $statusLabels[$status] ?? ucfirst($status);
                $items = $reservation['items'] ?? [];
            ?>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card reservation-card h-100">
                    <div class="card-body">
                        <h3 class="h6 mb-1">Réservation <?= htmlspecialchars(substr($reservation['id'], 0, 10)); ?></h3>
                        <p class="mb-2">
                            <span class="badge bg-info text-dark"><?= htmlspecialchars($statusLabel); ?></span>
                        </p>
                        <dl class="row small mb-0">
                            <dt class="col-5">Début</dt>
                            <dd class="col-7"><?= htmlspecialchars($reservation['start_date']); ?></dd>
                            <dt class="col-5">Retour prévu</dt>
                            <dd class="col-7"><?= htmlspecialchars($reservation['end_date']); ?></dd>
                            <dt class="col-12">Matériel</dt>
                            <dd class="col-12">
                                <ul class="list-unstyled mb-0 small">
                                    <?php foreach ($items as $item): ?>
                                        <?php
                                            $material = $materials[$item['material_id']] ?? null;
                                            $name = $material['name'] ?? $item['material_id'];
                                            $identifiers = $item['identifiers'] ?? [];
                                        ?>
                                        <li>
                                            <?= htmlspecialchars($name); ?>
                                            <span class="text-muted">(<?= htmlspecialchars($item['quantity']); ?>)</span>
                                            <?php if (!empty($identifiers)): ?>
                                                <span class="text-muted">– <?= htmlspecialchars(implode(', ', $identifiers)); ?></span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </dd>
                            <?php if (!empty($reservation['comment'])): ?>
                                <dt class="col-5">Commentaire</dt>
                                <dd class="col-7"><?= nl2br(htmlspecialchars($reservation['comment'])); ?></dd>
                            <?php endif; ?>
                        </dl>
                        <?php
                            $extension = $extensionOptions[$reservation['id']] ?? ['max_days' => 0];
                            $extensionStart = $extension['extension_start'] ?? null;
                            $maxEndDate = $extension['max_end_date'] ?? null;
                            $maxDays = (int) ($extension['max_days'] ?? 0);
                            $alreadyExtended = (bool) ($reservation['user_extension_used'] ?? false);
                        ?>
                        <?php if ($maxDays > 0 && $extensionStart && $maxEndDate): ?>
                            <hr>
                            <form action="<?= route('/reservations/extend'); ?>" method="POST" class="reservation-extend-form small">
                                <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($reservation['id']); ?>">
                                <div class="mb-2">
                                    <label for="extend_<?= htmlspecialchars($reservation['id']); ?>" class="form-label">Prolonger jusqu’au</label>
                                    <input type="date"
                                           class="form-control form-control-sm"
                                           id="extend_<?= htmlspecialchars($reservation['id']); ?>"
                                           name="new_end_date"
                                           value="<?= htmlspecialchars($maxEndDate); ?>"
                                           min="<?= htmlspecialchars($extensionStart); ?>"
                                           max="<?= htmlspecialchars($maxEndDate); ?>"
                                           required>
                                </div>
                                <div class="d-flex flex-column flex-sm-row gap-2">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Prolonger</button>
                                    <div class="text-muted align-self-center">
                                        Max. <?= $maxDays; ?> jour(s) supplémentaires
                                    </div>
                                </div>
                            </form>
                        <?php elseif ($alreadyExtended): ?>
                            <hr>
                            <p class="text-muted small mb-0">Prolongation déjà effectuée. Contactez un responsable pour une extension supplémentaire.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

