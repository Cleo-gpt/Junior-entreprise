<?php $layout = 'layouts/main'; ?>

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Corbeille</h1>
        <p class="text-muted mb-0">Restaurer ou supprimer définitivement les éléments supprimés.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= route('/admin/reservations'); ?>" class="btn btn-outline-secondary">Retour aux réservations</a>
    </div>
</div>

<?php if (empty($entries)): ?>
    <div class="empty-state">
        <img src="<?= asset('img/empty-box.svg'); ?>" alt="Corbeille vide">
        <h2 class="h5 mt-3">La corbeille est vide</h2>
        <p class="text-muted mb-0">Aucun élément n’attend de confirmation.</p>
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Détails</th>
                    <th>Supprimé par</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td class="text-nowrap"><?= htmlspecialchars(date('d.m.Y H:i', strtotime($entry['deleted_at'] ?? 'now'))); ?></td>
                        <td>
                            <span class="badge bg-secondary text-uppercase">
                                <?= htmlspecialchars($entry['type'] ?? 'inconnu'); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (($entry['type'] ?? '') === 'reservation'): ?>
                                <?php $payload = $entry['payload'] ?? []; ?>
                                <div class="fw-semibold">
                                    Réservation <?= htmlspecialchars(substr($payload['id'] ?? '—', 0, 10)); ?>
                                </div>
                                <div class="text-muted small">
                                    <?= htmlspecialchars($payload['start_date'] ?? '??'); ?> → <?= htmlspecialchars($payload['end_date'] ?? '??'); ?>
                                </div>
                                <div class="text-muted small">
                                    <?= count($payload['items'] ?? []); ?> article(s)
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">Détails indisponibles</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($entry['meta']['deleted_by_name'] ?? 'Inconnu'); ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($entry['meta']['deleted_by_role'] ?? ''); ?></div>
                        </td>
                        <td class="text-end text-nowrap">
                            <form action="<?= route('/admin/trash/restore'); ?>" method="POST" class="d-inline">
                                <input type="hidden" name="trash_id" value="<?= htmlspecialchars($entry['id']); ?>">
                                <button type="submit" class="btn btn-sm btn-outline-success">
                                    Restaurer
                                </button>
                            </form>
                            <form action="<?= route('/admin/trash/delete'); ?>" method="POST" class="d-inline ms-2" onsubmit="return confirm('Supprimer définitivement cet élément ?');">
                                <input type="hidden" name="trash_id" value="<?= htmlspecialchars($entry['id']); ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>


