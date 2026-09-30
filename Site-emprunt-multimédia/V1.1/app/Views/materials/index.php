<style>
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
@media (max-width:768px){.cat-spaces{grid-template-columns:repeat(3,minmax(0,1fr));max-width:100%;}}
@media (max-width:480px){.cat-spaces{grid-template-columns:repeat(2,minmax(0,1fr));}}
</style>

<div class="card shadow-sm mb-3">
    <div class="card-body py-3">
        <h1 class="h5 mb-1">Catalogue du matériel</h1>
        <p class="text-muted mb-0 small">Choisissez un espace pour consulter le matériel disponible.</p>
    </div>
</div>

<div class="cat-spaces">
    <?php foreach ($spaces as $space): ?>
        <a class="cat-space"
           href="<?= route('/materials?space=' . urlencode($space['slug'])); ?>"
           style="--space-accent: <?= htmlspecialchars($space['accent'], ENT_QUOTES, 'UTF-8'); ?>">
            <div class="cat-space__img">
                <img src="<?= asset($space['image']); ?>" alt="<?= htmlspecialchars($space['label'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="cat-space__txt">
                <?= htmlspecialchars($space['short'], ENT_QUOTES, 'UTF-8'); ?>
                <span class="cat-space__meta"><?= (int) $space['count']; ?> types</span>
            </div>
        </a>
    <?php endforeach; ?>
</div>
