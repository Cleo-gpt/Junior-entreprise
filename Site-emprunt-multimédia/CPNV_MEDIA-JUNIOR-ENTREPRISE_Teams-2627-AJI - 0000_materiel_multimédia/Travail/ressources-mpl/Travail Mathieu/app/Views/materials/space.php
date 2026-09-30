<style>
/* Catalogue espaces + produits — styles locaux (prioritaires) */
.cat-spaces{
  display:grid;
  grid-template-columns:repeat(5,minmax(0,1fr));
  gap:8px;
  max-width:560px;
}
.cat-space{
  display:block;
  height:88px;
  text-decoration:none;
  color:inherit;
  background:#fff;
  border-radius:10px;
  overflow:hidden;
  box-shadow:0 4px 12px rgba(17,38,60,.08);
  border-bottom:3px solid var(--space-accent,#002d62);
}
.cat-space:hover{transform:translateY(-2px);box-shadow:0 8px 16px rgba(17,38,60,.12);color:inherit;}
.cat-space__img{height:48px;display:flex;align-items:center;justify-content:center;background:#f3f6fb;padding:4px;}
.cat-space__img img{height:100%;width:auto;max-width:100%;object-fit:contain;}
.cat-space__txt{padding:4px 6px;font-size:12px;font-weight:700;color:#002d62;line-height:1.15;}
.cat-space__meta{display:block;font-size:10px;font-weight:500;color:#6f7d8f;}

.cat-products{
  display:grid;
  grid-template-columns:repeat(7,minmax(0,1fr));
  gap:8px;
  width:100%;
}
.cat-card{
  position:relative;
  width:100%;
  height:120px;
  margin:0;
  background:#fff;
  border-radius:10px;
  overflow:hidden;
  box-shadow:0 4px 12px rgba(17,38,60,.08);
  border-bottom:3px solid var(--space-accent,#002d62);
}
.cat-card.is-empty{opacity:.55;}
.cat-card__btn{
  display:flex;
  flex-direction:column;
  width:100%;
  height:100%;
  margin:0;padding:0;border:0;background:transparent;
  cursor:pointer;text-align:left;font:inherit;color:inherit;
}
.cat-card__stock{
  position:absolute;top:4px;right:4px;z-index:2;
  min-width:18px;padding:2px 5px;border-radius:999px;
  font-size:11px;font-weight:700;background:#e8f5e9;color:#1b5e20;line-height:1.1;
}
.cat-card__stock.out{background:#fdecea;color:#b71c1c;}
.cat-card__media{
  height:72px;display:flex;align-items:center;justify-content:center;
  background:#eef3f8;padding:6px;
}
.cat-card__media img{max-width:70%;max-height:70%;object-fit:contain;}
.cat-card__name{
  display:block;padding:4px 6px;font-size:11px;font-weight:700;color:#002d62;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}

/* Description UNIQUEMENT au survol */
.cat-card__hover{
  display:none;
  position:absolute;inset:0;z-index:5;
  flex-direction:column;justify-content:space-between;gap:4px;
  padding:8px;background:rgba(0,45,98,.95);color:#fff;overflow:hidden;
  pointer-events:none;
}
.cat-card:hover .cat-card__hover{display:flex;}
.cat-card__hover strong{font-size:11px;line-height:1.2;}
.cat-card__hover p{margin:0;font-size:10px;line-height:1.3;overflow:hidden;flex:1;}
.cat-card__hover em{font-style:normal;font-size:10px;font-weight:700;text-align:center;background:rgba(255,255,255,.16);border-radius:6px;padding:4px;}

/* Fiche détail matériel */
.mat-detail{
  position:fixed;inset:0;z-index:2000;
  display:flex;align-items:center;justify-content:center;
  padding:24px;
}
.mat-detail[hidden]{display:none!important;}
.mat-detail__backdrop{
  position:absolute;inset:0;background:rgba(8,20,38,.72);
  backdrop-filter:blur(2px);
}
.mat-detail__panel{
  position:relative;z-index:1;
  width:min(960px,100%);
  max-height:min(560px,calc(100vh - 48px));
  background:#fff;border-radius:16px;
  box-shadow:0 24px 48px rgba(8,20,38,.28);
  overflow:hidden;
  display:flex;flex-direction:column;
  border-bottom:4px solid var(--detail-accent,#002d62);
}
.mat-detail__close{
  position:absolute;top:10px;right:10px;z-index:5;
  width:36px;height:36px;border:0;border-radius:50%;
  background:rgba(255,255,255,.92);color:#002d62;
  font-size:22px;line-height:1;cursor:pointer;
  box-shadow:0 2px 8px rgba(0,0,0,.12);
}
.mat-detail__close:hover{background:#fff;}
.mat-detail__layout{
  display:grid;grid-template-columns:1fr 1fr;
  min-height:360px;max-height:min(560px,calc(100vh - 48px));
}
.mat-detail__carousel{
  position:relative;background:#eef3f8;
  display:flex;flex-direction:column;justify-content:center;
  padding:24px 16px 16px;
}
.mat-detail__slides{
  position:relative;flex:1;min-height:220px;
  display:flex;align-items:center;justify-content:center;
}
.mat-detail__slide{
  display:none;width:100%;height:100%;
  align-items:center;justify-content:center;
}
.mat-detail__slide.is-active{display:flex;}
.mat-detail__placeholder{
  width:min(280px,90%);aspect-ratio:4/3;
  border-radius:12px;background:linear-gradient(145deg,#d8e4f0,#b8c9dc);
  display:flex;align-items:center;justify-content:center;
  font-size:1.25rem;font-weight:700;color:#4a6278;
  box-shadow:inset 0 0 0 2px rgba(255,255,255,.5);
}
.mat-detail__nav{
  position:absolute;top:50%;transform:translateY(-50%);
  width:36px;height:36px;border:0;border-radius:50%;
  background:rgba(255,255,255,.9);color:#002d62;
  font-size:20px;line-height:1;cursor:pointer;
  box-shadow:0 2px 8px rgba(0,0,0,.1);
}
.mat-detail__nav:hover{background:#fff;}
.mat-detail__nav--prev{left:8px;}
.mat-detail__nav--next{right:8px;}
.mat-detail__dots{
  display:flex;justify-content:center;gap:6px;margin-top:12px;
}
.mat-detail__dot{
  width:8px;height:8px;border-radius:50%;border:0;padding:0;
  background:#b8c9dc;cursor:pointer;
}
.mat-detail__dot.is-active{background:#002d62;}

.mat-detail__info{
  padding:28px 24px 24px;
  overflow-y:auto;display:flex;flex-direction:column;gap:12px;
}
.mat-detail__info h2{
  margin:0;font-size:1.35rem;color:#002d62;line-height:1.25;
  padding-right:32px;
}
.mat-detail__stock{
  display:inline-flex;align-items:center;gap:6px;
  font-size:.875rem;font-weight:600;color:#1b5e20;
  background:#e8f5e9;border-radius:999px;padding:4px 12px;
  width:fit-content;
}
.mat-detail__stock.is-empty{color:#b71c1c;background:#fdecea;}
.mat-detail__desc{
  flex:1;margin:0;font-size:.925rem;line-height:1.55;color:#334155;
  white-space:pre-wrap;
}
.mat-detail__meta{
  font-size:.8rem;color:#64748b;margin:0;
}
.mat-detail__actions{margin-top:auto;padding-top:8px;}
.mat-detail__actions .btn{min-width:180px;}

@media (max-width:1200px){.cat-products{grid-template-columns:repeat(5,minmax(0,1fr));}}
@media (max-width:768px){
  .cat-spaces,.cat-products{grid-template-columns:repeat(3,minmax(0,1fr));max-width:100%;}
  .mat-detail{padding:12px;}
  .mat-detail__layout{grid-template-columns:1fr;max-height:calc(100vh - 24px);}
  .mat-detail__slides{min-height:160px;}
  .mat-detail__info{max-height:40vh;}
}
@media (max-width:480px){
  .cat-spaces,.cat-products{grid-template-columns:repeat(2,minmax(0,1fr));}
}
</style>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <a href="<?= route('/materials'); ?>" class="btn btn-sm btn-outline-secondary mb-1">← Retour aux espaces</a>
        <h1 class="h5 mb-0"><?= htmlspecialchars($space['label'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="text-muted small mb-0">
            <?= count($products); ?> types — survol = aperçu, clic = fiche détaillée
        </p>
    </div>
    <form class="d-flex gap-2" method="GET" action="<?= route('/materials'); ?>">
        <input type="hidden" name="space" value="<?= htmlspecialchars($space['slug'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="search" class="form-control form-control-sm" name="q" placeholder="Rechercher…" value="<?= htmlspecialchars($query ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <button class="btn btn-sm btn-primary" type="submit">Filtrer</button>
    </form>
</div>

<?php if (empty($products)): ?>
    <div class="empty-state">
        <img src="<?= asset('img/empty-search.svg'); ?>" alt="Aucun résultat">
        <h2 class="h5 mt-3">Aucun matériel trouvé</h2>
    </div>
<?php else: ?>
    <div class="cat-products">
        <?php foreach ($products as $index => $product):
            $remaining = (int) $product['remaining'];
            $available = $remaining > 0;
            $desc = trim(preg_replace('/\s+/u', ' ', (string) ($product['description'] ?? '')));
            if (function_exists('mb_strlen') && mb_strlen($desc, 'UTF-8') > 180) {
                $desc = mb_substr($desc, 0, 177, 'UTF-8') . '…';
            } elseif (strlen($desc) > 180) {
                $desc = substr($desc, 0, 177) . '...';
            }
            ?>
            <article class="cat-card <?= $available ? '' : 'is-empty'; ?>"
                     style="--space-accent: <?= htmlspecialchars($product['accent'], ENT_QUOTES, 'UTF-8'); ?>"
                     data-product-index="<?= (int) $index; ?>">
                <button type="button"
                        class="cat-card__btn"
                        aria-label="Voir la fiche : <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>">
                    <span class="cat-card__stock <?= $available ? '' : 'out'; ?>"><?= $remaining; ?></span>
                    <span class="cat-card__media"><img src="<?= asset($product['image']); ?>" alt=""></span>
                    <span class="cat-card__name"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                </button>

                <div class="cat-card__hover">
                    <strong><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    <p><?= htmlspecialchars($desc !== '' ? $desc : 'Aucune description.', ENT_QUOTES, 'UTF-8'); ?></p>
                    <em>Cliquer pour voir la fiche</em>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php
    $catalogJson = array_map(static function (array $product) use ($space) {
        return [
            'material_id' => $product['material_id'],
            'name' => $product['name'],
            'description' => trim((string) ($product['description'] ?? '')) ?: 'Aucune description disponible.',
            'remaining' => (int) $product['remaining'],
            'available' => (int) $product['remaining'] > 0,
            'marque' => $product['marque'] ?? '',
            'modele' => $product['modele'] ?? '',
            'categorie' => $product['categorie'] ?? '',
            'accent' => $product['accent'],
            'redirect' => '/materials?space=' . $space['slug'],
        ];
    }, $products);
    ?>

    <div class="mat-detail" id="materialDetail" hidden aria-hidden="true">
        <div class="mat-detail__backdrop" data-action="close-detail"></div>
        <div class="mat-detail__panel" role="dialog" aria-modal="true" aria-labelledby="matDetailTitle">
            <button type="button" class="mat-detail__close" data-action="close-detail" aria-label="Fermer">×</button>
            <div class="mat-detail__layout">
                <div class="mat-detail__carousel">
                    <button type="button" class="mat-detail__nav mat-detail__nav--prev" data-action="prev-slide" aria-label="Image précédente">‹</button>
                    <div class="mat-detail__slides" id="matDetailSlides"></div>
                    <button type="button" class="mat-detail__nav mat-detail__nav--next" data-action="next-slide" aria-label="Image suivante">›</button>
                    <div class="mat-detail__dots" id="matDetailDots"></div>
                </div>
                <div class="mat-detail__info">
                    <h2 id="matDetailTitle"></h2>
                    <span class="mat-detail__stock" id="matDetailStock"></span>
                    <p class="mat-detail__meta" id="matDetailMeta"></p>
                    <p class="mat-detail__desc" id="matDetailDesc"></p>
                    <div class="mat-detail__actions">
                        <form method="POST" action="<?= route('/cart/add'); ?>" id="matDetailCartForm">
                            <input type="hidden" name="material_id" id="matDetailMaterialId" value="">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="redirect" id="matDetailRedirect" value="">
                            <button type="submit" class="btn btn-primary" id="matDetailAddBtn">Ajouter au panier</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    $catalogJsonEncoded = json_encode(
        $catalogJson,
        JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_INVALID_UTF8_SUBSTITUTE
    );
    if ($catalogJsonEncoded === false) {
        $catalogJsonEncoded = '[]';
    }
    ?>
    <script type="application/json" id="catalog-products"><?= $catalogJsonEncoded; ?></script>
    <script>
    (function () {
      const detailRoot = document.getElementById('materialDetail');
      const productsEl = document.getElementById('catalog-products');
      if (!detailRoot || !productsEl) return;

      let products = [];
      try {
        products = JSON.parse(productsEl.textContent || '[]');
      } catch (e) {
        console.error('Catalogue matériel : JSON invalide', e);
        return;
      }

      const PLACEHOLDER_COUNT = 3;
      const titleEl = document.getElementById('matDetailTitle');
      const stockEl = document.getElementById('matDetailStock');
      const metaEl = document.getElementById('matDetailMeta');
      const descEl = document.getElementById('matDetailDesc');
      const slidesEl = document.getElementById('matDetailSlides');
      const dotsEl = document.getElementById('matDetailDots');
      const materialInput = document.getElementById('matDetailMaterialId');
      const redirectInput = document.getElementById('matDetailRedirect');
      const addBtn = document.getElementById('matDetailAddBtn');
      const panelEl = detailRoot.querySelector('.mat-detail__panel');
      let currentSlide = 0;
      let lastFocused = null;

      function buildCarousel() {
        if (!slidesEl || !dotsEl) return;
        slidesEl.innerHTML = '';
        dotsEl.innerHTML = '';
        for (let i = 0; i < PLACEHOLDER_COUNT; i++) {
          const slide = document.createElement('div');
          slide.className = 'mat-detail__slide' + (i === 0 ? ' is-active' : '');
          const placeholder = document.createElement('div');
          placeholder.className = 'mat-detail__placeholder';
          placeholder.textContent = 'Img ' + (i + 1);
          slide.appendChild(placeholder);
          slidesEl.appendChild(slide);

          const dot = document.createElement('button');
          dot.type = 'button';
          dot.className = 'mat-detail__dot' + (i === 0 ? ' is-active' : '');
          dot.dataset.slideIndex = String(i);
          dot.setAttribute('aria-label', 'Image ' + (i + 1));
          dotsEl.appendChild(dot);
        }
        currentSlide = 0;
      }

      function showSlide(index) {
        const slides = slidesEl.querySelectorAll('.mat-detail__slide');
        const dots = dotsEl.querySelectorAll('.mat-detail__dot');
        if (!slides.length) return;
        currentSlide = (index + slides.length) % slides.length;
        slides.forEach(function (slide, i) { slide.classList.toggle('is-active', i === currentSlide); });
        dots.forEach(function (dot, i) { dot.classList.toggle('is-active', i === currentSlide); });
      }

      function openDetail(index) {
        const product = products[index];
        if (!product) return;
        lastFocused = document.activeElement;
        titleEl.textContent = product.name || '';
        const remaining = Math.max(0, parseInt(product.remaining, 10) || 0);
        stockEl.textContent = remaining > 0
          ? remaining + ' disponible' + (remaining > 1 ? 's' : '')
          : 'Stock épuisé';
        stockEl.classList.toggle('is-empty', remaining <= 0);
        const parts = [product.categorie, product.marque, product.modele]
          .map(function (v) { return (v || '').trim(); })
          .filter(Boolean);
        metaEl.textContent = parts.join(' · ');
        metaEl.hidden = parts.length === 0;
        descEl.textContent = product.description || '';
        materialInput.value = product.material_id || '';
        redirectInput.value = product.redirect || '';
        if (panelEl && product.accent) panelEl.style.setProperty('--detail-accent', product.accent);
        addBtn.disabled = !product.available;
        addBtn.textContent = product.available ? 'Ajouter au panier' : 'Indisponible';
        buildCarousel();
        detailRoot.hidden = false;
        detailRoot.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        detailRoot.querySelector('.mat-detail__close')?.focus();
      }

      function closeDetail() {
        detailRoot.hidden = true;
        detailRoot.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (lastFocused && typeof lastFocused.focus === 'function') lastFocused.focus();
      }

      document.querySelector('.cat-products')?.addEventListener('click', function (event) {
        const card = event.target.closest('.cat-card[data-product-index]');
        if (!card || !card.contains(event.target)) return;
        if (event.target.closest('.mat-detail')) return;
        const index = parseInt(card.getAttribute('data-product-index'), 10);
        if (!isNaN(index)) {
          event.preventDefault();
          openDetail(index);
        }
      });

      detailRoot.addEventListener('click', function (event) {
        const actionEl = event.target.closest('[data-action]');
        if (!actionEl) return;
        const action = actionEl.getAttribute('data-action');
        if (action === 'close-detail') { closeDetail(); return; }
        if (action === 'prev-slide') { showSlide(currentSlide - 1); return; }
        if (action === 'next-slide') { showSlide(currentSlide + 1); return; }
        if (actionEl.classList.contains('mat-detail__dot')) {
          showSlide(parseInt(actionEl.dataset.slideIndex, 10) || 0);
        }
      });

      document.addEventListener('keydown', function (event) {
        if (detailRoot.hidden) return;
        if (event.key === 'Escape') closeDetail();
        if (event.key === 'ArrowLeft') showSlide(currentSlide - 1);
        if (event.key === 'ArrowRight') showSlide(currentSlide + 1);
      });
    })();
    </script>
<?php endif; ?>
