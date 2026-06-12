<?php

declare(strict_types=1);

function dispatch(string $path): void
{
    if (serve_static_if_exists($path)) {
        return;
    }

    $site = site_config();

    if ($path === '/robots.txt') {
        render_robots($site);
        return;
    }

    if ($path === '/sitemap.xml') {
        render_sitemap($site);
        return;
    }

    if ($path === '/') {
        $body = load_page_fragment('home');
        if ($body === null) {
            not_found();
            return;
        }

        $siteTitle = $site['title'] ?? 'Site';
        render_layout_page($site, 'Home', '/', $body, [
            'description' => (string) ($site['description'] ?? $site['tagline'] ?? ''),
            'keywords' => ['home'],
            'jsonLd' => site_base_url($site) !== null ? [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $siteTitle,
                'url' => absolute_url($site, '/'),
                'description' => (string) ($site['description'] ?? $site['tagline'] ?? ''),
            ] : null,
        ]);
        return;
    }

    if ($path === '/contact') {
        $body = load_page_fragment('contact');
        if ($body === null) {
            not_found();
            return;
        }

        render_layout_page($site, 'Contact', '/contact', $body, [
            'description' => (string) ($site['contactDescription'] ?? 'Get in touch with ' . ($site['title'] ?? 'us') . ' on Discord.'),
            'keywords' => ['contact', 'community'],
        ]);
        return;
    }

    if ($path === '/projects') {
        render_layout_page($site, 'Projects', '/projects', render_to_string('list', [
            'heading' => 'Projects',
            'collection' => 'projects',
            'entries' => list_entries('projects'),
        ]), [
            'description' => 'Projects from ' . ($site['title'] ?? 'the forge') . '.',
            'keywords' => ['projects'],
        ]);
        return;
    }

    if ($path === '/updates') {
        render_layout_page($site, 'Updates', '/updates', render_to_string('list', [
            'heading' => 'Updates',
            'collection' => 'updates',
            'entries' => list_entries('updates'),
        ]), [
            'description' => 'Build notes and updates from ' . ($site['title'] ?? 'the forge') . '.',
            'keywords' => ['updates'],
        ]);
        return;
    }

    if ($path === '/search') {
        render_layout_page($site, 'Search', '/search', render_to_string('search', [
            'query' => isset($_GET['q']) ? (string) $_GET['q'] : '',
            'results' => search_entries(isset($_GET['q']) ? (string) $_GET['q'] : ''),
        ]), [
            'description' => 'Search ' . ($site['title'] ?? 'the site') . '.',
            'noindex' => true,
        ]);
        return;
    }

    if (preg_match('#^/projects/([^/]+)$#', $path, $matches)) {
        $entry = find_entry('projects', $matches[1]);
        if ($entry === null) {
            not_found();
            return;
        }

        $title = (string) ($entry['data']['title'] ?? 'Project');
        $entryPath = '/projects/' . $entry['slug'];

        render_layout_page($site, $title, $entryPath, render_to_string('detail', [
            'collection' => 'projects',
            'entry' => $entry,
            'relatedUpdates' => updates_for_project($entry['slug']),
        ]), [
            'entry' => $entry['data'],
            'type' => 'website',
            'jsonLd' => build_project_json_ld($site, $entry, $title, $entryPath),
        ]);
        return;
    }

    if (preg_match('#^/updates/([^/]+)$#', $path, $matches)) {
        $entry = find_entry('updates', $matches[1]);
        if ($entry === null) {
            not_found();
            return;
        }

        $title = (string) ($entry['data']['title'] ?? 'Update');
        $entryPath = '/updates/' . $entry['slug'];

        render_layout_page($site, $title, $entryPath, render_to_string('detail', [
            'collection' => 'updates',
            'entry' => $entry,
            'linkedProjects' => projects_for_update($entry['data']),
        ]), [
            'entry' => $entry['data'],
            'type' => 'article',
            'jsonLd' => build_article_json_ld($site, $entry, $title, $entryPath),
        ]);
        return;
    }

    not_found();
}

function build_project_json_ld(array $site, array $entry, string $title, string $path): ?array
{
    if (site_base_url($site) === null) {
        return null;
    }

    return [
        '@context' => 'https://schema.org',
        '@type' => 'CreativeWork',
        'name' => $title,
        'url' => absolute_url($site, $path),
        'description' => entry_description($entry['data']),
    ];
}

function build_article_json_ld(array $site, array $entry, string $title, string $path): ?array
{
    if (site_base_url($site) === null) {
        return null;
    }

    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $title,
        'url' => absolute_url($site, $path),
        'description' => entry_description($entry['data']),
    ];

    if (!empty($entry['data']['date'])) {
        $data['datePublished'] = $entry['data']['date'];
    }

    return $data;
}

function render_to_string(string $template, array $vars = []): string
{
    ob_start();
    render($template, $vars);

    return (string) ob_get_clean();
}

function not_found(): void
{
    http_response_code(404);
    $site = site_config();
    render_layout_page($site, 'Not found', request_path(), '<p class="muted">That page does not exist.</p>', [
        'description' => 'Page not found.',
        'noindex' => true,
    ]);
}
