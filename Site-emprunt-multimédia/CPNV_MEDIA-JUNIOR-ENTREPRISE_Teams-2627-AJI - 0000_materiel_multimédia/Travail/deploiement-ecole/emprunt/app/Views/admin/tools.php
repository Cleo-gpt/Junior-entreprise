<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Outils administrateur</h1>
        <p class="text-muted mb-0">Ajoutez du matériel et suivez les notifications.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-5">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Ajouter un matériel</h2>
                <p class="text-muted">
                    Renseignez l’identifiant unique (ex. CAM-001), les informations principales et la quantité totale disponible.
                </p>
                <form action="<?= route('/admin/materials'); ?>" method="POST" class="row g-3" enctype="multipart/form-data">
                    <div class="col-12">
                        <label for="material_id" class="form-label">Identifiant</label>
                        <input type="text" class="form-control" id="material_id" name="material_id" placeholder="CAM-001" required>
                    </div>
                    <div class="col-12">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="quantity_total" class="form-label">Quantité totale</label>
                        <input type="number" min="0" class="form-control" id="quantity_total" name="quantity_total" value="1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mode de suivi</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tracking_mode" id="tracking_generic" value="generic" checked>
                            <label class="form-check-label" for="tracking_generic">
                                Standard (quantité globale)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tracking_mode" id="tracking_numbered" value="numbered">
                            <label class="form-check-label" for="tracking_numbered">
                                Numéroté (identifiants individuels)
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="identifiers" class="form-label">Identifiants individuels</label>
                        <textarea class="form-control" id="identifiers" name="identifiers" rows="3" placeholder="Saisissez un identifiant par ligne" data-role="identifiers-field"></textarea>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2 d-none" data-action="generate-identifiers">
                            Générer automatiquement (001, 002, 003…)
                        </button>
                        <div class="form-text">
                            Obligatoire pour les matériels numérotés (ex. CAM-001-1, CAM-001-2). Laisser vide pour un matériel classique.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="replacement_cost" class="form-label">Prix indicatif (CHF)</label>
                        <input type="number" class="form-control" id="replacement_cost" name="replacement_cost" min="0" step="0.05" placeholder="Ex. 249.90">
                        <div class="form-text">
                            Référence interne en cas de perte ou casse. Non visible par les utilisateurs.
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Images (couverture et galerie)</label>
                        <div class="material-images-manager" data-max-size="5242880" data-mode="create">
                            <input type="hidden" name="cover_choice" data-role="cover-input" value="">
                            <div class="mb-2">
                                <input
                                    type="file"
                                    class="form-control file-size-guard"
                                    id="material_images"
                                    name="material_images[]"
                                    multiple
                                    accept="image/*"
                                    data-max-size="5242880"
                                    data-warning-target="material_images_warning_new"
                                    data-role="file-input"
                                >
                                <div class="form-text">
                                    Ajoutez une ou plusieurs images (5 Mo max chacune). La première devient la couverture par défaut.
                                </div>
                                <div id="material_images_warning_new" class="text-danger small d-none"></div>
                            </div>
                            <div class="material-images-list d-flex flex-wrap gap-3 mt-3 d-none" data-role="card-list"></div>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2 d-none" data-action="clear-all">
                                Supprimer toutes les images importées
                            </button>
                            <div class="mt-3">
                                <label for="material_images_urls" class="form-label">Ajouter via URL (une par ligne)</label>
                                <textarea
                                    class="form-control"
                                    id="material_images_urls"
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
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-7">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Inventaire existant</h2>
                <?php if (empty($materials)): ?>
                    <p class="text-muted mb-0">Aucun matériel enregistré pour le moment.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Identifiant</th>
                                    <th>Nom</th>
                                    <th>Images</th>
                                    <th>Disponible</th>
                                    <th>Total</th>
                                    <th>Suivi</th>
                                    <th>Prix indicatif</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materials as $item): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= htmlspecialchars($item['id']); ?></td>
                                        <td><?= htmlspecialchars($item['name']); ?></td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <?php if (!empty($item['cover_image'])): ?>
                                                    <span class="badge bg-info text-dark">Couverture</span>
                                                <?php endif; ?>
                                                <span class="text-muted small">
                                                    <?= count($item['gallery']); ?> image<?= count($item['gallery']) > 1 ? 's' : ''; ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $item['status'] === 'available' ? 'success' : 'secondary'; ?>">
                                                <?= $item['quantity_available']; ?>
                                            </span>
                                        </td>
                                        <td><?= $item['quantity_total']; ?></td>
                                        <td>
                                            <?php if (($item['tracking_mode'] ?? 'generic') === 'numbered'): ?>
                                                <span class="badge bg-warning text-dark">
                                                    Numéroté (<?= count($item['identifiers'] ?? []); ?>)
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Standard</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= isset($item['replacement_cost']) && $item['replacement_cost'] !== null
                                                ? number_format((float) $item['replacement_cost'], 2, '.', '\'') . ' CHF'
                                                : '<span class="text-muted">—</span>'; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= route('/admin/materials/edit'); ?>?id=<?= urlencode($item['id']); ?>" class="btn btn-sm btn-outline-primary">
                                                Modifier
                                            </a>
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
        <h2 class="h5 mb-3">Modèles d’emails</h2>
        <p class="text-muted">
            Prévisualisez les courriels envoyés aux étudiants (confirmation, rappel, etc.). En mode développement, les emails sont enregistrés dans <code>storage/emails/</code>.
        </p>
        <form action="<?= route('/admin/preview-email'); ?>" method="POST" class="row g-3">
            <div class="col-12 col-md-6 col-xl-3">
                <label for="template" class="form-label">Modèle</label>
                <select class="form-select" id="template" name="template">
                    <option value="reservation_confirmation">Confirmation de réservation</option>
                    <option value="reservation_reminder">Rappel de restitution</option>
                </select>
            </div>
            <div class="col-12 col-md-6 col-xl-3 align-self-end">
                <button type="submit" class="btn btn-outline-primary">Générer un aperçu</button>
            </div>
        </form>
        <?php if (!empty($_GET['preview'])): ?>
            <?php
            $template = $_GET['preview'];
            $sample = (new \App\Services\NotificationService())->buildReservationEmail($template, [
                'user_name' => 'Jean Dupont',
                'material_name' => 'Caméra 4K',
                'start_date' => '2025-01-10',
                'end_date' => '2025-01-12',
            ]);
            ?>
            <div class="mt-4 p-3 border rounded bg-light">
                <h3 class="h6">Sujet</h3>
                <p class="mb-2"><?= htmlspecialchars($sample['subject']); ?></p>
                <h3 class="h6">Contenu</h3>
                <pre class="mb-0 small" style="white-space: pre-wrap;"><?= htmlspecialchars($sample['body']); ?></pre>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form[action="<?= route('/admin/materials'); ?>"]');
  if (!form) {
    return;
  }

  const identifiersField = form.querySelector('[data-role="identifiers-field"]');
  const radios = form.querySelectorAll('input[name="tracking_mode"]');
  const generateButton = form.querySelector('[data-action="generate-identifiers"]');
  const materialInput = form.querySelector('#material_id');
  const quantityInput = form.querySelector('#quantity_total');

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
      generateIdentifiers();
    }
  };

  const generateIdentifiers = () => {
    if (!identifiersField) {
      return;
    }

    const mode = form.querySelector('input[name="tracking_mode"]:checked')?.value ?? 'generic';
    if (mode !== 'numbered') {
      return;
    }

    const prefixRaw = materialInput?.value?.trim() ?? '';
    const prefix = prefixRaw !== '' ? prefixRaw : 'ITEM';
    const quantity = parseInt(quantityInput?.value ?? '0', 10);

    if (!Number.isFinite(quantity) || quantity <= 0) {
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
  materialInput?.addEventListener('change', () => {
    const mode = form.querySelector('input[name="tracking_mode"]:checked')?.value ?? 'generic';
    if (mode === 'numbered' && identifiersField && (identifiersField.value.trim() === '' || identifiersField.dataset.autogenerated === '1')) {
      generateIdentifiers();
    }
  });
  quantityInput?.addEventListener('change', () => {
    const mode = form.querySelector('input[name="tracking_mode"]:checked')?.value ?? 'generic';
    if (mode === 'numbered' && identifiersField && (identifiersField.dataset.autogenerated === '1' || identifiersField.value.trim() === '')) {
      generateIdentifiers();
    }
  });

  generateButton?.addEventListener('click', event => {
    event.preventDefault();
    generateIdentifiers();
  });

  toggleIdentifiers();
});
</script>

