document.addEventListener('DOMContentLoaded', () => {
  initReserveModal();
  initValidation();
  initFileSizeGuards();
  initMaterialImageManagers();
  initCartDateGuards();
});

function initReserveModal() {
  const reserveModal = document.getElementById('reserveModal');
  if (!reserveModal) {
    return;
  }

  reserveModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const materialId = button?.getAttribute('data-material') ?? '';
    const available = Math.max(0, parseInt(button?.getAttribute('data-available') ?? '0', 10));
    const defaultStart = button?.getAttribute('data-default-start') ?? '';
    const defaultEnd = button?.getAttribute('data-default-end') ?? '';

    const materialInput = reserveModal.querySelector('#material_id');
    const quantityInput = reserveModal.querySelector('#quantity');
    const quantityHelp = reserveModal.querySelector('#quantityHelp');
    const startInput = reserveModal.querySelector('#start_date');
    const endInput = reserveModal.querySelector('#end_date');

    if (materialInput) {
      materialInput.value = materialId;
    }

    if (quantityInput) {
      quantityInput.max = available > 0 ? available : 1;
      quantityInput.value = available > 0 ? '1' : '0';
      quantityInput.min = available > 0 ? 1 : 0;
      quantityInput.toggleAttribute('disabled', available === 0);
      quantityInput.setAttribute('aria-valuemax', String(Math.max(available, 1)));
    }

    if (quantityHelp) {
      quantityHelp.textContent = available > 0
        ? `Sélectionnez le nombre d’exemplaires (max. ${available}).`
        : 'Aucun exemplaire disponible pour cette période.';
    }

    if (startInput && defaultStart) {
      startInput.value = defaultStart;
    }

    if (endInput && defaultEnd) {
      endInput.value = defaultEnd;
    }
  });
}

function initValidation() {
  document.querySelectorAll('.needs-validation').forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  });
}

function initFileSizeGuards() {
  const formatBytes = bytes => {
    const units = ['octets', 'Ko', 'Mo', 'Go'];
    let index = 0;
    let value = bytes;

    while (value >= 1024 && index < units.length - 1) {
      value /= 1024;
      index += 1;
    }

    return `${value.toFixed(index === 0 ? 0 : 1)} ${units[index]}`;
  };

  document.querySelectorAll('.file-size-guard').forEach(input => {
    const maxSize = parseInt(input.dataset.maxSize ?? '0', 10);
    const warningId = input.dataset.warningTarget;
    const warningEl = warningId ? document.getElementById(warningId) : null;

    if (!maxSize || !warningEl) {
      return;
    }

    input.addEventListener('change', () => {
      const files = input.files ? Array.from(input.files) : [];
      const hasError = files.some(file => file.size > maxSize);

      if (hasError) {
        warningEl.textContent = `Chaque image doit peser au maximum ${formatBytes(maxSize)}.`;
        warningEl.classList.remove('d-none');
        input.value = '';
      } else {
        warningEl.textContent = '';
        warningEl.classList.add('d-none');
      }
    });
  });
}

function initMaterialImageManagers() {
  document.querySelectorAll('.material-images-manager').forEach(manager => {
    setupMaterialImagesManager(manager);
  });
}

function initCartDateGuards() {
  const startInput = document.getElementById('start_date');
  const endInput = document.getElementById('end_date');

  if (!startInput || !endInput) {
    return;
  }

  const blockedDatesRaw = startInput.dataset.blockedDates ?? '[]';
  let blockedDates;
  try {
    blockedDates = JSON.parse(blockedDatesRaw);
  } catch (error) {
    blockedDates = [];
  }

  const blockedSet = new Set(Array.isArray(blockedDates) ? blockedDates : []);
  const horizon = startInput.dataset.horizon ?? '';

  const addDays = (dateString, amount) => {
    const date = new Date(dateString);
    if (Number.isNaN(date.getTime())) {
      return '';
    }
    date.setDate(date.getDate() + amount);
    return date.toISOString().slice(0, 10);
  };

  const formatDate = dateString => {
    const date = new Date(dateString);
    if (Number.isNaN(date.getTime())) {
      return dateString;
    }
    return date.toLocaleDateString('fr-CH');
  };

  const firstAvailableFrom = value => {
    let cursor = value;
    const compareLimit = horizon || addDays(value, 365);

    while (cursor && cursor <= compareLimit) {
      if (!blockedSet.has(cursor)) {
        return cursor;
      }
      cursor = addDays(cursor, 1);
    }

    return '';
  };

  const rangeHasBlock = (startValue, endValue) => {
    if (!startValue || !endValue) {
      return false;
    }

    let cursor = startValue;
    while (cursor <= endValue) {
      if (blockedSet.has(cursor)) {
        return cursor;
      }
      cursor = addDays(cursor, 1);
    }
    return null;
  };

  const applyMinMax = () => {
    if (startInput.value) {
      endInput.min = startInput.value;
      if (!endInput.value || endInput.value < startInput.value) {
        endInput.value = startInput.value;
      }
    }
  };

  const validate = () => {
    const startValue = startInput.value;
    const endValue = endInput.value;

    startInput.setCustomValidity('');
    endInput.setCustomValidity('');

    if (startValue && blockedSet.has(startValue)) {
      startInput.setCustomValidity(`La date ${formatDate(startValue)} est indisponible.`);
      startInput.reportValidity();
      return;
    }

    if (endValue && startValue && endValue < startValue) {
      endInput.setCustomValidity('La date de retour doit être postérieure à la date de retrait.');
      endInput.reportValidity();
      return;
    }

    const blockedInRange = rangeHasBlock(startValue, endValue);
    if (blockedInRange) {
      endInput.setCustomValidity(`Le matériel n’est pas disponible le ${formatDate(blockedInRange)}.`);
      endInput.reportValidity();
    }
  };

  startInput.addEventListener('change', () => {
    const value = startInput.value;
    if (value && blockedSet.has(value)) {
      const replacement = firstAvailableFrom(value);
      if (replacement) {
        startInput.value = replacement;
      } else {
        startInput.value = '';
      }
    }
    applyMinMax();
    validate();
  });

  endInput.addEventListener('change', () => {
    validate();
  });

  applyMinMax();
  validate();
}

function setupMaterialImagesManager(manager) {
  const coverInput = manager.querySelector('[data-role="cover-input"]');
  const cardList = manager.querySelector('[data-role="card-list"]');
  const fileInput = manager.querySelector('[data-role="file-input"]');
  const clearButton = manager.querySelector('[data-action="clear-all"]');
  const urlInput = manager.querySelector('[data-role="url-input"]');
  const addUrlsButton = manager.querySelector('[data-action="add-urls"]');
  const maxSize = parseInt(manager.dataset.maxSize ?? '0', 10);

  if (!cardList || !coverInput) {
    return;
  }

  const state = {
    files: [], // { file, preview }
    urls: [], // { url }
  };

  const parseIdentifier = identifier => {
    const [type, value] = (identifier || '').split('::', 2);
    return { type: type ?? '', value: value ?? '' };
  };

  const updateCoverHighlights = () => {
    const cards = Array.from(cardList.querySelectorAll('[data-image-card]'));
    const hasCards = cards.length > 0;
    if (!hasCards) {
      cardList.classList.add('d-none');
      if (coverInput) {
        coverInput.value = '';
      }
    } else {
      cardList.classList.remove('d-none');
    }

    const current = coverInput.value;
    cards.forEach(card => {
      const isCover = current !== '' && card.dataset.coverValue === current;
      card.classList.toggle('is-cover', isCover);
      const badge = card.querySelector('[data-role="cover-badge"]');
      if (badge) {
        badge.classList.toggle('d-none', !isCover);
      }
    });

    const hasTransient = state.files.length > 0 || state.urls.length > 0;
    if (clearButton) {
      clearButton.classList.toggle('d-none', !hasTransient);
    }
  };

  const fallbackCover = () => {
    const firstCard = cardList.querySelector('[data-image-card]');
    if (firstCard) {
      coverInput.value = firstCard.dataset.coverValue ?? '';
    } else {
      coverInput.value = '';
    }
  };

  const rebuildFileInput = () => {
    if (!fileInput) {
      return;
    }
    const dt = new DataTransfer();
    state.files.forEach(entry => dt.items.add(entry.file));
    fileInput.files = dt.files;
    if (!state.files.length) {
      fileInput.value = '';
    }
  };

  const createCard = ({ identifier, src, type, hiddenInput }) => {
    const card = document.createElement('div');
    card.className = 'material-image-card';
    card.dataset.imageCard = '1';
    card.dataset.coverValue = identifier;
    card.dataset.type = type;

    if (hiddenInput) {
      card.appendChild(hiddenInput);
    }

    const img = document.createElement('img');
    img.src = src;
    img.alt = 'Image';
    card.appendChild(img);

    const badge = document.createElement('span');
    badge.className = 'badge bg-primary text-white cover-badge d-none';
    badge.dataset.role = 'cover-badge';
    badge.textContent = 'Image principale';
    card.appendChild(badge);

    const actions = document.createElement('div');
    actions.className = 'material-image-actions';

    const coverBtn = document.createElement('button');
    coverBtn.type = 'button';
    coverBtn.className = 'btn btn-sm btn-outline-primary';
    coverBtn.dataset.action = 'set-cover';
    coverBtn.textContent = 'Définir comme principale';
    actions.appendChild(coverBtn);

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'btn btn-sm btn-outline-danger';
    removeBtn.dataset.action = 'remove-image';
    removeBtn.textContent = 'Supprimer';
    actions.appendChild(removeBtn);

    card.appendChild(actions);
    cardList.appendChild(card);

    attachCardHandlers(card);
    return card;
  };

  const attachCardHandlers = card => {
    const identifier = card.dataset.coverValue ?? '';
    const coverBtn = card.querySelector('[data-action="set-cover"]');
    const removeBtn = card.querySelector('[data-action="remove-image"]');
    const img = card.querySelector('img');

    const setCover = () => {
      coverInput.value = identifier;
      updateCoverHighlights();
    };

    coverBtn?.addEventListener('click', event => {
      event.preventDefault();
      setCover();
    });

    img?.addEventListener('click', setCover);

    removeBtn?.addEventListener('click', event => {
      event.preventDefault();
      const { type, value } = parseIdentifier(identifier);

      if (type === 'existing') {
        card.querySelector('input[name="existing_images[]"]')?.remove();
        card.remove();
        fallbackCover();
        updateCoverHighlights();
        return;
      }

      if (type === 'new') {
        const index = parseInt(value, 10);
        if (!Number.isNaN(index)) {
          state.files.splice(index, 1);
          renderNewCards();
        }
        return;
      }

      if (type === 'url') {
        const index = parseInt(value, 10);
        if (!Number.isNaN(index)) {
          state.urls.splice(index, 1);
          renderUrlCards();
        }
        return;
      }
    });
  };

  const renderNewCards = () => {
    cardList
      .querySelectorAll('[data-image-card][data-type="new"]')
      .forEach(card => card.remove());

    state.files.forEach((entry, index) => {
      if (!entry.preview) {
        return;
      }

      entry.identifier = `new::${index}`;
      createCard({
        identifier: entry.identifier,
        src: entry.preview,
        type: 'new',
      });
    });

    rebuildFileInput();

    const current = parseIdentifier(coverInput.value);
    if (current.type === 'new') {
      const idx = parseInt(current.value, 10);
      if (Number.isNaN(idx) || idx >= state.files.length) {
        fallbackCover();
      }
    } else if (!coverInput.value) {
      fallbackCover();
    }

    updateCoverHighlights();
  };

  const renderUrlCards = () => {
    cardList
      .querySelectorAll('[data-image-card][data-type="url"]')
      .forEach(card => card.remove());

    state.urls.forEach((entry, index) => {
      entry.identifier = `url::${index}`;
      const hidden = document.createElement('input');
      hidden.type = 'hidden';
      hidden.name = 'remote_images[]';
      hidden.value = entry.url;

      createCard({
        identifier: entry.identifier,
        src: entry.url,
        type: 'url',
        hiddenInput: hidden,
      });
    });

    const current = parseIdentifier(coverInput.value);
    if (current.type === 'url') {
      const idx = parseInt(current.value, 10);
      if (Number.isNaN(idx) || idx >= state.urls.length) {
        fallbackCover();
      }
    } else if (!coverInput.value) {
      fallbackCover();
    }

    updateCoverHighlights();
  };

  fileInput?.addEventListener('change', () => {
    const files = Array.from(fileInput.files ?? []);
    if (!files.length) {
      return;
    }

    files.forEach(file => {
      if (maxSize && file.size > maxSize) {
        return;
      }

      const reader = new FileReader();
      reader.onload = e => {
        const preview = e.target?.result ?? '';
        state.files.push({ file, preview });
        renderNewCards();
      };
      reader.readAsDataURL(file);
    });
  });

  clearButton?.addEventListener('click', event => {
    event.preventDefault();
    state.files = [];
    state.urls = [];
    cardList
      .querySelectorAll('[data-image-card][data-type="new"], [data-image-card][data-type="url"]')
      .forEach(card => card.remove());
    rebuildFileInput();
    fallbackCover();
    updateCoverHighlights();
  });

  addUrlsButton?.addEventListener('click', event => {
    event.preventDefault();
    if (!urlInput) {
      return;
    }
    const lines = urlInput.value
      .split(/\r?\n/)
      .map(line => line.trim())
      .filter(Boolean);

    if (!lines.length) {
      return;
    }

    lines.forEach(url => {
      state.urls.push({ url });
    });

    urlInput.value = '';
    renderUrlCards();
  });

  // Initialisation des cartes existantes (mode édition)
  cardList.querySelectorAll('[data-image-card][data-type="existing"]').forEach(card => {
    attachCardHandlers(card);
  });

  if (!coverInput.value) {
    fallbackCover();
  }

  updateCoverHighlights();
}

