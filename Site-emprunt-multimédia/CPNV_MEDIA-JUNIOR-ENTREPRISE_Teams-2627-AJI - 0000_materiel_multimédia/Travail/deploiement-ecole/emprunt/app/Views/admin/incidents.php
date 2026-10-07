<?php $layout = 'layouts/main'; ?>

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Incidents et Matériel Endommagé</h1>
        <p class="text-muted mb-0">Suivi des objets perdus, volés, cassés ou endommagés.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= route('/admin/reservations'); ?>" class="btn btn-outline-secondary">Retour aux réservations</a>
        <a href="<?= route('/admin/tools'); ?>" class="btn btn-outline-secondary">Gestion du matériel</a>
    </div>
</div>

<?php if (empty($incidents)): ?>
    <div class="empty-state">
        <img src="<?= asset('img/empty-box.svg'); ?>" alt="Aucun incident">
        <h2 class="h5 mt-3">Aucun incident signalé</h2>
        <p class="text-muted mb-0">Tout le matériel est en bon état.</p>
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Matériel</th>
                        <th>Utilisateur</th>
                        <th>Statut</th>
                        <th>Description / Condition</th>
                        <th class="text-end">Prix indicatif</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incidents as $incident): ?>
                        <tr>
                            <td class="text-nowrap">
                                <?= date('d/m/Y', strtotime($incident['date'])); ?>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($incident['material_name']); ?></div>
                                <?php if (!empty($incident['identifiers'])): ?>
                                    <div class="text-muted small">
                                        Ref: <?= htmlspecialchars(implode(', ', $incident['identifiers'])); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($incident['user_name']); ?></div>
                                <div class="text-muted small"><?= htmlspecialchars($incident['user_email']); ?></div>
                            </td>
                            <td>
                                <?php
                                    $badgeClass = match($incident['status']) {
                                        'lost' => 'bg-danger',
                                        'broken' => 'bg-dark',
                                        'damaged' => 'bg-warning text-dark',
                                        default => 'bg-secondary'
                                    };
                                    $label = match($incident['status']) {
                                        'lost' => 'Perdu/Volé',
                                        'broken' => 'Cassé',
                                        'damaged' => 'Endommagé',
                                        default => $incident['status']
                                    };
                                ?>
                                <span class="badge <?= $badgeClass; ?>">
                                    <?= htmlspecialchars($label); ?>
                                </span>
                            </td>
                            <td>
                                <?= nl2br(htmlspecialchars($incident['condition'])); ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <?php if ($incident['replacement_cost']): ?>
                                    <?= number_format((float) $incident['replacement_cost'], 2, '.', '\''); ?> CHF
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <!-- Link to reservation context -->
                                <a href="<?= route('/admin/reservations'); ?>#res-<?= $incident['reservation_id']; ?>" class="btn btn-sm btn-outline-primary" title="Voir la réservation">
                                    Voir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

