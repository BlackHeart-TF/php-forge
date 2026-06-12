<?php

declare(strict_types=1);

function site_base_url(array $site): ?string
{
    if (empty($site['url']) || !is_string($site['url'])) {
        return null;
    }

    return rtrim($site['url'], '/');
}

function absolute_url(array $site, string $path): ?string
{
    $base = site_base_url($site);
    if ($base === null) {
        return null;
    }

    if ($path === '/' || $path === '') {
        return $base . '/';
    }

    return $base . $path;
}

function entry_description(array $data, string $fallback = ''): string
{
    if (!empty($data['description']) && is_string($data['description'])) {
        return $data['description'];
    }

    if (!empty($data['stub']) && is_string($data['stub'])) {
        return $data['stub'];
    }

    return $fallback;
}

function entry_og_image(array $site, array $data = []): ?string
{
    if (!empty($data['ogImage']) && is_string($data['ogImage'])) {
        return absolute_url($site, $data['ogImage']) ?? $data['ogImage'];
    }

    if (!empty($data['images'][0]) && is_string($data['images'][0])) {
        $image = $data['images'][0];

        return absolute_url($site, $image) ?? $image;
    }

    if (!empty($site['ogImage']) && is_string($site['ogImage'])) {
        return absolute_url($site, $site['ogImage']) ?? $site['ogImage'];
    }

    $logo = site_logo($site);
    if ($logo !== null) {
        return absolute_url($site, $logo) ?? $logo;
    }

    return null;
}

function page_document_title(array $site, string $title): string
{
    $siteTitle = $site['title'] ?? 'Site';

    return $title === 'Home' ? $siteTitle : $title . ' · ' . $siteTitle;
}

/** @return list<string> */
function normalize_keyword_list(mixed $value): array
{
    if (!is_array($value)) {
        return [];
    }

    $keywords = [];
    foreach ($value as $item) {
        if (is_string($item) && trim($item) !== '') {
            $keywords[] = trim($item);
        }
    }

    return $keywords;
}

/** @param list<string> ...$lists */
function merge_keywords(array ...$lists): array
{
    $seen = [];
    $merged = [];

    foreach ($lists as $list) {
        foreach ($list as $keyword) {
            $key = mb_strtolower($keyword);
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $merged[] = $keyword;
        }
    }

    return $merged;
}

/** @param array<string, mixed> $options */
function build_page_keywords(array $site, array $options = [], array $entry = []): string
{
    $keywords = merge_keywords(
        normalize_keyword_list($site['keywords'] ?? []),
        normalize_keyword_list($options['keywords'] ?? []),
        normalize_keyword_list($entry['tags'] ?? []),
        normalize_keyword_list($entry['keywords'] ?? []),
    );

    return implode(', ', $keywords);
}

/** @param array<string, mixed> $options */
function build_page_meta(array $site, string $title, string $path, array $options = []): array
{
    $entry = is_array($options['entry'] ?? null) ? $options['entry'] : [];
    $fallbackDescription = (string) ($site['description'] ?? $site['tagline'] ?? '');
    $description = (string) ($options['description'] ?? entry_description($entry, $fallbackDescription));
    $keywords = build_page_keywords($site, $options, $entry);

    return [
        'title' => page_document_title($site, $title),
        'description' => $description,
        'keywords' => $keywords,
        'canonical' => absolute_url($site, $path),
        'ogType' => (string) ($options['type'] ?? 'website'),
        'ogImage' => $options['image'] ?? entry_og_image($site, $entry),
        'noindex' => (bool) ($options['noindex'] ?? false),
        'jsonLd' => $options['jsonLd'] ?? null,
    ];
}

/** @return list<string> */
function sitemap_paths(): array
{
    $paths = ['/', '/contact', '/projects', '/updates'];

    foreach (list_entries('projects') as $entry) {
        $paths[] = '/projects/' . $entry['slug'];
    }

    foreach (list_entries('updates') as $entry) {
        $paths[] = '/updates/' . $entry['slug'];
    }

    return $paths;
}

function render_sitemap(array $site): void
{
    header('Content-Type: application/xml; charset=utf-8');

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach (sitemap_paths() as $path) {
        $loc = absolute_url($site, $path);
        if ($loc === null) {
            continue;
        }

        echo '  <url><loc>' . e($loc) . '</loc></url>' . "\n";
    }

    echo '</urlset>';
}

function render_robots(array $site): void
{
    header('Content-Type: text/plain; charset=utf-8');

    echo "User-agent: *\n";
    echo "Allow: /\n";
    echo "Disallow: /search\n";

    $sitemap = absolute_url($site, '/sitemap.xml');
    if ($sitemap !== null) {
        echo 'Sitemap: ' . $sitemap . "\n";
    }
}

/** @param array<string, mixed> $metaOpts */
function render_layout_page(array $site, string $title, string $path, string $body, array $metaOpts = []): void
{
    render('layout', [
        'site' => $site,
        'title' => $title,
        'body' => $body,
        'meta' => build_page_meta($site, $title, $path, $metaOpts),
    ]);
}
