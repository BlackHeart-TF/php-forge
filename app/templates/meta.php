<?php
/** @var array $meta */
/** @var array $site */
?>
    <meta name="description" content="<?= e($meta['description'] ?? '') ?>">
<?php if (!empty($meta['keywords'])): ?>
    <meta name="keywords" content="<?= e($meta['keywords']) ?>">
<?php endif ?>
<?php if (!empty($meta['noindex'])): ?>
    <meta name="robots" content="noindex, follow">
<?php endif ?>
<?php if (!empty($meta['canonical'])): ?>
    <link rel="canonical" href="<?= e($meta['canonical']) ?>">
<?php endif ?>
    <meta property="og:site_name" content="<?= e($site['title'] ?? 'Site') ?>">
    <meta property="og:title" content="<?= e($meta['title'] ?? '') ?>">
    <meta property="og:description" content="<?= e($meta['description'] ?? '') ?>">
    <meta property="og:type" content="<?= e($meta['ogType'] ?? 'website') ?>">
<?php if (!empty($meta['canonical'])): ?>
    <meta property="og:url" content="<?= e($meta['canonical']) ?>">
<?php endif ?>
<?php if (!empty($meta['ogImage'])): ?>
    <meta property="og:image" content="<?= e($meta['ogImage']) ?>">
<?php endif ?>
    <meta name="twitter:card" content="<?= !empty($meta['ogImage']) ? 'summary_large_image' : 'summary' ?>">
    <meta name="twitter:title" content="<?= e($meta['title'] ?? '') ?>">
    <meta name="twitter:description" content="<?= e($meta['description'] ?? '') ?>">
<?php if (!empty($meta['ogImage'])): ?>
    <meta name="twitter:image" content="<?= e($meta['ogImage']) ?>">
<?php endif ?>
<?php if (!empty($meta['jsonLd'])): ?>
    <script type="application/ld+json"><?= json_encode($meta['jsonLd'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php endif ?>
