<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Gestion des utilisateurs</h1>
        <p class="text-muted mb-0">Créez des comptes, ajustez les rôles et gérez les accès étudiants.</p>
    </div>
    <a href="<?= route('/admin/tools'); ?>" class="btn btn-outline-secondary">Outils matériel</a>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Créer un utilisateur</h2>
                <form action="<?= route('/admin/users/create'); ?>" method="POST" class="row g-3">
                    <div class="col-12">
                        <label for="user_name" class="form-label">Nom complet</label>
                        <input type="text" class="form-control" id="user_name" name="name" required>
                    </div>
                    <div class="col-12">
                        <label for="user_email" class="form-label">Email @eduvaud</label>
                        <input type="email" class="form-control" id="user_email" name="email" required>
                    </div>
                    <div class="col-12">
                        <label for="user_password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="user_password" name="password" required minlength="8">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Rôles</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($roles as $role): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="<?= htmlspecialchars($role); ?>" id="new_role_<?= htmlspecialchars($role); ?>" name="roles[]"
                                        <?= $role === 'etudiant' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="new_role_<?= htmlspecialchars($role); ?>">
                                        <?= htmlspecialchars($roleLabels[$role] ?? ucfirst($role)); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-text">Au moins un rôle est requis (par défaut : étudiant).</div>
                    </div>
                    <div class="col-12">
                        <label for="user_status" class="form-label">Statut</label>
                        <select class="form-select" id="user_status" name="status">
                            <?php foreach ($statuses as $statusOption): ?>
                                <option value="<?= htmlspecialchars($statusOption); ?>" <?= $statusOption === 'active' ? 'selected' : ''; ?>>
                                    <?= ucfirst($statusOption); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100">Créer l’utilisateur</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Utilisateurs enregistrés</h2>
                <?php if (empty($users)): ?>
                    <p class="text-muted mb-0">Aucun utilisateur enregistré pour le moment.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Identité</th>
                                    <th>Permissions</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?= htmlspecialchars($item['name']); ?></div>
                                            <div class="text-muted small"><?= htmlspecialchars($item['email']); ?></div>
                                            <div class="text-muted small">Créé le <?= date('d.m.Y', strtotime($item['created_at'] ?? 'now')); ?></div>
                                        </td>
                                        <td>
                                            <form action="<?= route('/admin/users/update'); ?>" method="POST" class="d-flex flex-column gap-2">
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($item['id']); ?>">
                                                <div class="d-flex flex-wrap gap-2">
                                                    <?php foreach ($roles as $role): ?>
                                                        <?php $inputId = 'role_' . $item['id'] . '_' . $role; ?>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="<?= htmlspecialchars($inputId); ?>" name="roles[]" value="<?= htmlspecialchars($role); ?>"
                                                                <?= in_array($role, $item['roles'] ?? [], true) ? 'checked' : ''; ?>>
                                                            <label class="form-check-label small" for="<?= htmlspecialchars($inputId); ?>">
                                                                <?= htmlspecialchars($roleLabels[$role] ?? ucfirst($role)); ?>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <select class="form-select form-select-sm" name="status">
                                                    <?php foreach ($statuses as $statusOption): ?>
                                                        <option value="<?= htmlspecialchars($statusOption); ?>" <?= ($item['status'] ?? '') === $statusOption ? 'selected' : ''; ?>>
                                                            <?= ucfirst($statusOption); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-outline-primary align-self-start">Mettre à jour</button>
                                            </form>
                                        </td>
                                        <td class="text-end">
                                            <form action="<?= route('/admin/users/delete'); ?>" method="POST" class="d-inline">
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($item['id']); ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    <?= ($item['id'] === ($user['id'] ?? '')) ? 'disabled' : ''; ?>>
                                                    Supprimer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h2 class="h5 mb-3">Liste blanche étudiants</h2>
        <div class="row g-4">
            <div class="col-12 col-lg-6">
                <h3 class="h6">Ajouter une adresse</h3>
                <form action="<?= route('/admin/whitelist/add'); ?>" method="POST" class="row g-3 align-items-end">
                    <div class="col-12">
                        <label for="whitelist_email" class="form-label">Adresse @eduvaud</label>
                        <input type="email" class="form-control" id="whitelist_email" name="email" placeholder="prenom.nom@eduvaud.ch" required>
                    </div>
                    <div class="col-sm-6">
                        <label for="whitelist_starts_at" class="form-label">Début (optionnel)</label>
                        <input type="date" class="form-control" id="whitelist_starts_at" name="starts_at">
                    </div>
                    <div class="col-sm-6">
                        <label for="whitelist_ends_at" class="form-label">Fin (optionnel)</label>
                        <input type="date" class="form-control" id="whitelist_ends_at" name="ends_at">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-outline-primary">Ajouter à la liste blanche</button>
                    </div>
                </form>
            </div>
            <div class="col-12 col-lg-6">
                <h3 class="h6">Import CSV</h3>
                <form action="<?= route('/admin/whitelist/import'); ?>" method="POST" class="row g-3 align-items-end" enctype="multipart/form-data">
                    <div class="col-12">
                        <label for="whitelist_csv" class="form-label">Fichier CSV</label>
                        <input type="file" class="form-control" id="whitelist_csv" name="whitelist_csv" accept=".csv" required>
                        <div class="form-text">Colonnes attendues : <code>email</code>, <code>start_date</code>, <code>end_date</code> (au format YYYY-MM-DD). En-tête optionnel.</div>
                    </div>
                    <div class="col-sm-6">
                        <label for="csv_default_start" class="form-label">Début par défaut</label>
                        <input type="date" class="form-control" id="csv_default_start" name="default_starts_at">
                    </div>
                    <div class="col-sm-6">
                        <label for="csv_default_end" class="form-label">Fin par défaut</label>
                        <input type="date" class="form-control" id="csv_default_end" name="default_ends_at">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-outline-secondary">Importer le fichier</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($whitelist)): ?>
            <hr>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $today = date('Y-m-d');
                        foreach ($whitelist as $entry):
                            $startsAt = $entry['starts_at'] ?? null;
                            $endsAt = $entry['ends_at'] ?? null;
                            $isUnlimited = !$startsAt && !$endsAt;
                            $statusLabel = $isUnlimited ? 'Illimité' : 'Actif';
                            $statusClass = $isUnlimited ? 'primary' : 'success';

                            if (!$entry['active'] && !$isUnlimited) {
                                if ($endsAt && $endsAt < $today) {
                                    $statusLabel = 'Expiré';
                                    $statusClass = 'secondary';
                                } elseif ($startsAt && $startsAt > $today) {
                                    $statusLabel = 'À venir';
                                    $statusClass = 'warning';
                                } else {
                                    $statusLabel = 'Inactif';
                                    $statusClass = 'secondary';
                                }
                            }
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($entry['email']); ?></td>
                                <td><?= $startsAt ? htmlspecialchars(date('d.m.Y', strtotime($startsAt))) : '—'; ?></td>
                                <td><?= $endsAt ? htmlspecialchars(date('d.m.Y', strtotime($endsAt))) : '—'; ?></td>
                                <td>
                                    <span class="badge bg-<?= $statusClass; ?>">
                                        <?= htmlspecialchars($statusLabel); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form action="<?= route('/admin/whitelist/remove'); ?>" method="POST" class="d-inline">
                                        <input type="hidden" name="email" value="<?= htmlspecialchars($entry['email']); ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Retirer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted mb-0 mt-3">Aucune adresse autorisée pour le moment.</p>
        <?php endif; ?>
    </div>
</div>

