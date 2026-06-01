<?php
/** @var array $site */
/** @var string $title */
/** @var string $body */
/** @var bool $is_fragment */
$is_fragment = $is_fragment ?? false;
$siteTitle = $site['title'] ?? 'Site';
$pageTitle = ($title === 'Home' ? '' : $title . ' · ') . $siteTitle;
$logo = site_logo($site);
$logoAlt = site_logo_alt($site);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($site['tagline'] ?? '') ?>">
    <link rel="stylesheet" href="/style.css">
    <?php if ($logo !== null): ?>
        <link rel="icon" href="<?= e($logo) ?>" type="image/svg+xml">
    <?php endif ?>
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand-lockup" href="/">
                <?php if ($logo !== null): ?>
                    <img class="brand-logo" src="<?= e($logo) ?>" alt="<?= e($logoAlt) ?>" width="56" height="56" decoding="async">
                <?php endif ?>
                <span class="brand-text">
                    <span class="brand"><?= e($siteTitle) ?></span>
                    <?php if (!empty($site['tagline'])): ?>
                        <span class="tagline"><?= e($site['tagline']) ?></span>
                    <?php endif ?>
                </span>
            </a>

            <nav class="nav" aria-label="Main">
                <a href="/" class="<?= nav_is_active('/') ? 'is-active' : '' ?>">Home</a>
                <a href="/projects" class="<?= nav_is_active('/projects') ? 'is-active' : '' ?>">Projects</a>
                <a href="/updates" class="<?= nav_is_active('/updates') ? 'is-active' : '' ?>">Updates</a>
                <a href="/contact" class="<?= nav_is_active('/contact') ? 'is-active' : '' ?>">Contact</a>
                <form class="search-form" action="/search" method="get">
                    <input type="search" name="q" placeholder="Search…" aria-label="Search">
                </form>
            </nav>
        </div>
    </header>

    <main class="content">
        <?= $body ?>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <?php if ($logo !== null): ?>
                <img class="footer-logo" src="<?= e($logo) ?>" alt="" width="32" height="32" decoding="async">
            <?php endif ?>
            <p class="muted"><?= e($site['footer'] ?? $siteTitle) ?></p>
        </div>
    </footer>
</body>
</html>
