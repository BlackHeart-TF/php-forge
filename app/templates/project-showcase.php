<?php
/** @var list<string> $images */
/** @var string $content */
$firstImage = $images[0] ?? '';
?>
<section class="project-showcase<?= $images === [] ? ' project-showcase--text-only' : '' ?>" data-project-showcase>
    <?php if ($images !== []): ?>
    <div class="project-showcase__media">
        <button type="button" class="project-showcase__main" data-showcase-zoom aria-label="Zoom screenshot">
            <img src="<?= e($firstImage) ?>" alt="" data-showcase-main decoding="async">
        </button>
        <?php if (count($images) > 1): ?>
        <div class="project-showcase__thumbs">
            <?php foreach ($images as $index => $image): ?>
                <button
                    type="button"
                    class="project-showcase__thumb<?= $index === 0 ? ' is-active' : '' ?>"
                    data-showcase-index="<?= (int) $index ?>"
                    aria-label="Show screenshot <?= (int) $index + 1 ?>"
                >
                    <img src="<?= e($image) ?>" alt="" loading="lazy" decoding="async">
                </button>
            <?php endforeach ?>
        </div>
        <?php endif ?>
    </div>
    <?php endif ?>

    <?php if (trim($content) !== ''): ?>
    <div class="project-showcase__prose prose">
        <?= $content ?>
    </div>
    <?php endif ?>
</section>

<?php if ($images !== []): ?>
<dialog class="lightbox" data-lightbox aria-label="Screenshot viewer">
    <button type="button" class="lightbox__close" data-lightbox-close aria-label="Close">×</button>
    <button type="button" class="lightbox__nav lightbox__nav--prev" data-lightbox-prev aria-label="Previous">‹</button>
    <div class="lightbox__stage">
        <img class="lightbox__img" src="" alt="" data-lightbox-img>
    </div>
    <button type="button" class="lightbox__nav lightbox__nav--next" data-lightbox-next aria-label="Next">›</button>
</dialog>

<script>
(() => {
  const showcase = document.querySelector("[data-project-showcase]");
  const dialog = document.querySelector("[data-lightbox]");
  if (!showcase || !dialog) return;

  const mainImg = showcase.querySelector("[data-showcase-main]");
  const thumbs = [...showcase.querySelectorAll("[data-showcase-index]")];
  const images = thumbs.length
    ? thumbs.map((btn) => btn.querySelector("img").src)
    : mainImg ? [mainImg.src] : [];

  if (!images.length) return;

  const imgEl = dialog.querySelector("[data-lightbox-img]");
  let index = 0;

  const setMain = (i) => {
    index = (i + images.length) % images.length;
    if (mainImg) mainImg.src = images[index];
    thumbs.forEach((btn, n) => btn.classList.toggle("is-active", n === index));
  };

  const openLightbox = () => {
    imgEl.src = images[index];
    dialog.querySelector("[data-lightbox-prev]").hidden = images.length < 2;
    dialog.querySelector("[data-lightbox-next]").hidden = images.length < 2;
    dialog.showModal();
  };

  thumbs.forEach((btn) => {
    btn.addEventListener("click", () => setMain(Number(btn.dataset.showcaseIndex)));
  });

  showcase.querySelector("[data-showcase-zoom]")?.addEventListener("click", openLightbox);

  dialog.querySelector("[data-lightbox-close]").addEventListener("click", () => dialog.close());
  dialog.querySelector("[data-lightbox-prev]").addEventListener("click", () => {
    setMain(index - 1);
    imgEl.src = images[index];
  });
  dialog.querySelector("[data-lightbox-next]").addEventListener("click", () => {
    setMain(index + 1);
    imgEl.src = images[index];
  });

  dialog.addEventListener("click", (e) => {
    if (e.target === dialog) dialog.close();
  });
})();
</script>
<?php endif ?>
