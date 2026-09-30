<?php
$selectedStart = $selectedStart ?? (new DateTimeImmutable('today'))->format('Y-m-d');
$selectedEnd = $selectedEnd ?? (new DateTimeImmutable('today +7 days'))->format('Y-m-d');
$availability = $availability ?? [];
$overlaps = $overlaps ?? [];
$upcoming = $upcoming ?? [];
$statusLabels = [
    'pending' => 'En attente',
    'approved' => 'Validée',
    'checked_out' => 'Retirée',
    'returned' => 'Restituée',
    'cancelled' => 'Annulée',
];
?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
            <div>
                <h1 class="h4 mb-1">Catalogue du matériel</h1>
                <p class="text-muted mb-0">
                    Choisissez votre période pour visualiser les disponibilités et planifier vos réservations.
                </p>
            </div>
            <form class="row g-2 align-items-end" method="GET">
                <div class="col-12 col-lg-auto">
                    <label for="catalog_search" class="form-label small mb-1">Recherche</label>
                    <input type="search"
                           class="form-control"
                           id="catalog_search"
                           name="q"
                           placeholder="Nom du matériel"
                           value="<?= htmlspecialchars($_GET['q'] ?? ''); ?>">
                </div>
                <div class="col-12 col-md-auto">
                    <label for="catalog_start" class="form-label small mb-1">Du</label>
                    <input type="date"
                           class="form-control"
                           id="catalog_start"
                           name="start_date"
                           value="<?= htmlspecialchars($selectedStart); ?>"
                           required>
                </div>
                <div class="col-12 col-md-auto">
                    <label for="catalog_end" class="form-label small mb-1">Au</label>
                    <input type="date"
                           class="form-control"
                           id="catalog_end"
                           name="end_date"
                           value="<?= htmlspecialchars($selectedEnd); ?>"
                           required>
                </div>
                <div class="col-12 col-lg-auto">
                    <button class="btn btn-primary w-100" type="submit">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$query = strtolower(trim($_GET['q'] ?? ''));
$filtered = $query
    ? array_values(array_filter($materials, fn($item) => str_contains(strtolower($item['name']), $query)))
    : $materials;
?>

<?php if (empty($filtered)): ?>
    <div class="empty-state">
        <img src="<?= asset('img/empty-search.svg'); ?>" alt="Aucun résultat">
        <h2 class="h5 mt-3">Aucun matériel trouvé</h2>
        <p class="text-muted mb-0">Essayez avec un autre mot-clé ou parcourez la liste complète.</p>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($filtered as $material):
            $images = [];

            $coverPath = $material['cover_image'] ?? '';
            if ($coverPath) {
                $images[] = $coverPath;
            }

            foreach ($material['gallery'] as $extra) {
                if ($extra !== $coverPath) {
                    $images[] = $extra;
                }
            }

            if (!$images) {
                $images[] = asset('img/sample-camera.svg');
            } else {
                $images = array_map(function ($path) {
                    if (preg_match('#^https?://#i', $path)) {
                        return $path;
                    }

                    return route($path);
                }, $images);
            }

            $galleryCount = count($images);
            $carouselId = 'carousel_' . htmlspecialchars($material['id']);
        ?>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card equipment-card h-100">
                    <?php if ($galleryCount > 1): ?>
                        <div id="<?= $carouselId; ?>" class="carousel slide equipment-carousel">
                            <div class="carousel-inner">
                                <?php foreach ($images as $index => $imgSrc): ?>
                                    <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>">
                                        <img src="<?= htmlspecialchars($imgSrc); ?>" class="d-block w-100 equipment-carousel-img" alt="<?= htmlspecialchars($material['name']); ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#<?= $carouselId; ?>" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Précédent</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#<?= $carouselId; ?>" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Suivant</span>
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="equipment-cover position-relative">
                            <img src="<?= htmlspecialchars($images[0]); ?>" class="card-img-top" alt="<?= htmlspecialchars($material['name']); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h2 class="h5"><?= htmlspecialchars($material['name']); ?></h2>
                        <p class="text-muted flex-grow-1"><?= htmlspecialchars($material['description']); ?></p>
                        <?php
                            $materialId = $material['id'];
                            $periodAvailable = $availability[$materialId] ?? $material['quantity_available'];
                            $isAvailable = $periodAvailable > 0;
                            $overlapping = $overlaps[$materialId] ?? [];
                            $upcomingList = $upcoming[$materialId] ?? [];
                        ?>
                        <p class="mb-2">
                            <span class="badge bg-<?= $isAvailable ? 'success' : 'danger'; ?>">
                                <?= $periodAvailable; ?> disponible(s) du <?= htmlspecialchars(date('d.m.Y', strtotime($selectedStart))); ?>
                                au <?= htmlspecialchars(date('d.m.Y', strtotime($selectedEnd))); ?>
                            </span>
                            <span class="badge bg-secondary"><?= $material['quantity_total']; ?> total</span>
                        </p>
                        <div class="mb-3">
                            <?php if (!empty($overlapping)): ?>
                                <p class="fw-semibold small mb-1">Réservations sur cette période :</p>
                                <ul class="list-unstyled small mb-0">
                                    <?php foreach ($overlapping as $entry): ?>
                                        <li>
                                            <?= htmlspecialchars(date('d.m', strtotime($entry['start_date']))); ?>
                                            →
                                            <?= htmlspecialchars(date('d.m', strtotime($entry['end_date']))); ?>
                                            · <?= (int) $entry['quantity']; ?> ex.
                                            <span class="text-muted">
                                                <?= htmlspecialchars($statusLabels[$entry['status']] ?? ucfirst($entry['status'])); ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted small mb-0">Aucune réservation sur cette période.</p>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($upcomingList)): ?>
                            <div class="mb-3">
                                <p class="fw-semibold small mb-1">À venir :</p>
                                <ul class="list-unstyled small mb-0">
                                    <?php foreach ($upcomingList as $entry): ?>
                                        <li>
                                            <?= htmlspecialchars(date('d.m', strtotime($entry['start_date']))); ?>
                                            →
                                            <?= htmlspecialchars(date('d.m', strtotime($entry['end_date']))); ?>
                                            · <?= (int) $entry['quantity']; ?> ex.
                                            <span class="text-muted">
                                                <?= htmlspecialchars($statusLabels[$entry['status']] ?? ucfirst($entry['status'])); ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex flex-column gap-2">
                            <button class="btn btn-outline-primary w-100"
                                    data-bs-toggle="modal"
                                    data-bs-target="#reserveModal"
                                    data-material="<?= htmlspecialchars($material['id']); ?>"
                                    data-available="<?= max(0, $periodAvailable); ?>"
                                    data-default-start="<?= htmlspecialchars($selectedStart); ?>"
                                    data-default-end="<?= htmlspecialchars($selectedEnd); ?>"
                                    <?= $isAvailable ? '' : 'disabled'; ?>>
                                Réserver immédiatement
                            </button>
                            <form action="<?= route('/cart/add'); ?>" method="POST" class="d-flex gap-2">
                                <input type="hidden" name="material_id" value="<?= htmlspecialchars($material['id']); ?>">
                                <input type="number"
                                       name="quantity"
                                       class="form-control form-control-sm cart-add-input"
                                       min="1"
                                       max="<?= $material['quantity_total']; ?>"
                                       value="1">
                                <button type="submit"
                                        class="btn btn-sm btn-outline-secondary flex-grow-1">
                                    Ajouter au panier
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="modal fade" id="reserveModal" tabindex="-1" aria-labelledby="reserveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="<?= route('/reservations'); ?>" method="POST">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="reserveModalLabel">Réserver le matériel</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="material_id" id="material_id" value="">
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantité</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" step="1" required>
                    <div class="form-text" id="quantityHelp">Sélectionnez le nombre d’exemplaires (max. selon disponibilité).</div>
                </div>
                <div class="mb-3">
                    <label for="start_date" class="form-label">Date de retrait</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                </div>
                <div class="mb-3">
                    <label for="end_date" class="form-label">Date de retour</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                </div>
                <div class="mb-3">
                    <label for="comment" class="form-label">Commentaire (optionnel)</label>
                    <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Valider la réservation</button>
            </div>
        </form>
    </div>
</div>

