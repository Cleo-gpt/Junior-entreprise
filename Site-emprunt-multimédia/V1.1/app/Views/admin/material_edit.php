<?php
$materialGallery = $material['gallery'] ?? [];
$images = [];

if (!empty($material['cover_image'])) {
    $images[] = [
        'path' => $material['cover_image'],
        'is_cover' => true,
    ];
}

foreach ($materialGallery as $path) {
    $images[] = [
        'path' => $path,
        'is_cover' => false,
    ];
}

$coverChoice = '';
$hasCover = false;
foreach ($images as $index => $image) {
    if ($image['is_cover'] && !$hasCover) {
        $coverChoice = 'existing::' . base64_encode($image['path']);
        $hasCover = true;
    } else {
        $images[$index]['is_cover'] = false;
    }
}

if (!$hasCover && !empty($images)) {
    $images[0]['is_cover'] = true;
    $coverChoice = 'existing::' . base64_encode($images[0]['path']);
}

$reservedCount = max(0, (int) $material['quantity_total'] - (int) $material['quantity_available']);
$availableCount = (int) $material['quantity_available'];
$trackingMode = $material['tracking_mode'] ?? 'generic';
$identifiersList = implode("\n", $material['identifiers'] ?? []);
?>

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Modifier le matériel</h1>
        <p class="text-muted mb-0">Ajustez les informations et l’inventaire de <?= htmlspecialchars($material['name']); ?>.</p>
    </div>
    <a href="<?= route('/admin/tools'); ?>" class="btn btn-outline-secondary">Retour à la gestion du matériel</a>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-7">
        <div class="card h-100">
            <div class="card-body">
                <form action="<?= route('/admin/materials/update'); ?>" method="POST" class="row g-3" enctype="multipart/form-data">
                    <input type="hidden" name="material_id" value="<?= htmlspecialchars($material['id']); ?>">
                    <input type="hidden" name="cover_choice" data-role="cover-input" value="<?= htmlspecialchars($coverChoice); ?>">

                    <div class="col-md-6">
                        <label for="material_id_display" class="form-label">Identifiant</label>
                        <input type="text" class="form-control" id="material_id_display" value="<?= htmlspecialchars($material['id']); ?>" disabled>
                        <div class="form-text">L’identifiant ne peut pas être modifié.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="quantity_total" class="form-label">Quantité totale</label>
                        <input type="number" class="form-control" id="quantity_total" name="quantity_total"
                               value="<?= (int) $material['quantity_total']; ?>" min="<?= $reservedCount; ?>" required>
                        <div class="form-text">
                            Réservés : <?= $reservedCount; ?> &nbsp;|&nbsp; Disponibles : <?= $availableCount; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mode de suivi</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tracking_mode" id="tracking_generic" value="generic"
                                <?= $trackingMode !== 'numbered' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="tracking_generic">
                                Standard (quantité globale)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tracking_mode" id="tracking_numbered" value="numbered"
                                <?= $trackingMode === 'numbered' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="tracking_numbered">
                                Numéroté (identifiants individuels)
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="identifiers" class="form-label">Identifiants individuels</label>
                        <textarea class="form-control" id="identifiers" name="identifiers" rows="3" data-role="identifiers-field"
                                  placeholder="Un identifiant par ligne"><?= htmlspecialchars($identifiersList); ?></textarea>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2 d-none" data-action="generate-identifiers">
                            Générer automatiquement (001, 002, 003…)
                        </button>
                        <div class="form-text">
                            Obligatoire pour les matériels numérotés. La quantité totale doit correspondre au nombre d’identifiants.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="replacement_cost" class="form-label">Prix indicatif (CHF)</label>
                        <input type="number" class="form-control" id="replacement_cost" name="replacement_cost"
                               min="0" step="0.05"
                               value="<?= isset($material['replacement_cost']) && $material['replacement_cost'] !== null ? htmlspecialchars(number_format((float) $material['replacement_cost'], 2, '.', '')) : ''; ?>"
                               placeholder="Ex. 249.90">
                        <div class="form-text">
                            Référence interne en cas de perte/casse. Non visible côté étudiant.
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($material['name']); ?>" required>
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($material['description']); ?></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Images (couverture et galerie)</label>
                        <div class="material-images-manager" data-max-size="5242880" data-mode="edit">
                            <div class="material-images-list d-flex flex-wrap gap-3 mt-2 <?= empty($images) ? 'd-none' : ''; ?>" data-role="card-list">
                                <?php foreach ($images as $image): ?>
                                    <?php
                                        $path = $image['path'];
                                        $coverValue = 'existing::' . base64_encode($path);
                                        $isCover = $image['is_cover'];
                                        $src = preg_match('#^https?://#i', $path) ? $path : route($path);
                                    ?>
                                    <div class="material-image-card<?= $isCover ? ' is-cover' : ''; ?>"
                                         data-image-card
                                         data-type="existing"
                                         data-cover-value="<?= htmlspecialchars($coverValue); ?>">
                                        <input type="hidden" name="existing_images[]" value="<?= htmlspecialchars($path); ?>">
                                        <img src="<?= htmlspecialchars($src); ?>" alt="Image existante">
                                        <span class="badge bg-primary text-white cover-badge<?= $isCover ? '' : ' d-none'; ?>" data-role="cover-badge">Image principale</span>
                                        <div class="material-image-actions mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-action="set-cover">Définir comme principale</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-action="remove-image">Supprimer</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="mb-2">
                                <input
                                    type="file"
                                    class="form-control file-size-guard"
                                    id="material_images_edit"
                                    name="material_images[]"
                                    multiple
                                    accept="image/*"
                                    data-max-size="5242880"
                                    data-warning-target="material_images_warning_edit"
                                    data-role="file-input"
                                >
                                <div class="form-text">
                                    Ajoutez de nouvelles images (5 Mo max). Cliquez sur une carte pour définir l’image principale.
                                </div>
                                <div id="material_images_warning_edit" class="text-danger small d-none"></div>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-danger mt-2 d-none" data-action="clear-all">
                                Supprimer toutes les images importées
                            </button>

                            <div class="mt-3">
                                <label for="material_images_urls_edit" class="form-label">Ajouter via URL (une par ligne)</label>
                                <textarea
                                    class="form-control"
                                    id="material_images_urls_edit"
                                    name="material_images_urls"
                                    rows="2"
                                    placeholder="https://...\nhttps://..."
                                    data-role="url-input"
                                ></textarea>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-action="add-urls">
                                    Ajouter les URLs
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                        <a href="<?= route('/admin/tools'); ?>" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Informations</h2>
                <p class="mb-1"><strong>Disponibles :</strong> <?= $availableCount; ?></p>
                <p class="mb-1"><strong>Réservés :</strong> <?= $reservedCount; ?></p>
                <p class="mb-1">
                    <strong>Suivi :</strong>
                    <?= $trackingMode === 'numbered'
                        ? 'Numéroté (' . count($material['identifiers'] ?? []) . ' identifiants)'
                        : 'Standard'; ?>
                </p>
                <p class="mb-0">
                    <strong>Prix indicatif :</strong>
                    <?= isset($material['replacement_cost']) && $material['replacement_cost'] !== null
                        ? number_format((float) $material['replacement_cost'], 2, '.', '\'') . ' CHF'
                        : '—'; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form[action="<?= route('/admin/materials/update'); ?>"]');
  if (!form) {
    return;
  }

  const identifiersField = form.querySelector('[data-role="identifiers-field"]');
  const radios = form.querySelectorAll('input[name="tracking_mode"]');
  const generateButton = form.querySelector('[data-action="generate-identifiers"]');
  const quantityInput = form.querySelector('#quantity_total');
  const materialHiddenInput = form.querySelector('input[name="material_id"]');

  const markManual = () => {
    if (identifiersField) {
      identifiersField.dataset.autogenerated = '0';
    }
  };

  const toggleIdentifiers = () => {
    const mode = form.querySelector('input[name="tracking_mode"]:checked')?.value ?? 'generic';
    if (identifiersField) {
      const container = identifiersField.closest('.col-12');
      container?.classList.toggle('d-none', mode !== 'numbered');
      identifiersField.toggleAttribute('required', mode === 'numbered');
      identifiersField.dataset.autogenerated = identifiersField.dataset.autogenerated ?? '0';
    }
    if (generateButton) {
      generateButton.classList.toggle('d-none', mode !== 'numbered');
    }

    if (mode === 'numbered' && identifiersField && identifiersField.value.trim() === '') {
      generateIdentifiers(true);
    }
  };

  const generateIdentifiers = (force = false) => {
    if (!identifiersField) {
      return;
    }

    const mode = form.querySelector('input[name="tracking_mode"]:checked')?.value ?? 'generic';
    if (mode !== 'numbered') {
      return;
    }

    const prefixRaw = materialHiddenInput?.value?.trim() ?? '';
    const prefix = prefixRaw !== '' ? prefixRaw : 'ITEM';
    const quantity = parseInt(quantityInput?.value ?? '0', 10);

    if (!Number.isFinite(quantity) || quantity <= 0) {
      return;
    }

    if (!force && identifiersField.dataset.autogenerated !== '1' && identifiersField.value.trim() !== '') {
      return;
    }

    const lines = [];
    for (let i = 1; i <= quantity; i += 1) {
      lines.push(`${prefix}-${String(i).padStart(3, '0')}`);
    }

    identifiersField.value = lines.join('\n');
    identifiersField.dataset.autogenerated = '1';
  };

  radios.forEach(radio => {
    radio.addEventListener('change', toggleIdentifiers);
  });

  identifiersField?.addEventListener('input', markManual);
  quantityInput?.addEventListener('change', () => {
    if (identifiersField && (identifiersField.dataset.autogenerated === '1' || identifiersField.value.trim() === '')) {
      generateIdentifiers(true);
    }
  });

  generateButton?.addEventListener('click', event => {
    event.preventDefault();
    generateIdentifiers(true);
  });

  toggleIdentifiers();
});
</script>

