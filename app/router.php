<?php

declare(strict_types=1);

function dispatch(string $path): void
{
    if (serve_static_if_exists($path)) {
        return;
    }

    $site = site_config();

    if ($path === '/') {
        $body = load_page_fragment('home');
        if ($body === null) {
            not_found();
            return;
        }

        render('layout', [
            'site' => $site,
            'title' => 'Home',
            'body' => $body,
            'is_fragment' => true,
        ]);
        return;
    }

    if ($path === '/contact') {
        $body = load_page_fragment('contact');
        if ($body === null) {
            not_found();
            return;
        }

        render('layout', [
            'site' => $site,
            'title' => 'Contact',
            'body' => $body,
            'is_fragment' => true,
        ]);
        return;
    }

    if ($path === '/projects') {
        render('layout', [
            'site' => $site,
            'title' => 'Projects',
            'body' => render_to_string('list', [
                'heading' => 'Projects',
                'collection' => 'projects',
                'entries' => list_entries('projects'),
            ]),
        ]);
        return;
    }

    if ($path === '/updates') {
        render('layout', [
            'site' => $site,
            'title' => 'Updates',
            'body' => render_to_string('list', [
                'heading' => 'Updates',
                'collection' => 'updates',
                'entries' => list_entries('updates'),
            ]),
        ]);
        return;
    }

    if ($path === '/search') {
        $query = isset($_GET['q']) ? (string) $_GET['q'] : '';
        render('layout', [
            'site' => $site,
            'title' => 'Search',
            'body' => render_to_string('search', [
                'query' => $query,
                'results' => search_entries($query),
            ]),
        ]);
        return;
    }

    if (preg_match('#^/projects/([^/]+)$#', $path, $matches)) {
        $entry = find_entry('projects', $matches[1]);
        if ($entry === null) {
            not_found();
            return;
        }

        render('layout', [
            'site' => $site,
            'title' => (string) ($entry['data']['title'] ?? 'Project'),
            'body' => render_to_string('detail', [
                'collection' => 'projects',
                'entry' => $entry,
                'relatedUpdates' => updates_for_project($entry['slug']),
            ]),
        ]);
        return;
    }

    if (preg_match('#^/updates/([^/]+)$#', $path, $matches)) {
        $entry = find_entry('updates', $matches[1]);
        if ($entry === null) {
            not_found();
            return;
        }

        render('layout', [
            'site' => $site,
            'title' => (string) ($entry['data']['title'] ?? 'Update'),
            'body' => render_to_string('detail', [
                'collection' => 'updates',
                'entry' => $entry,
                'linkedProjects' => projects_for_update($entry['data']),
            ]),
        ]);
        return;
    }

    not_found();
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
    render('layout', [
        'site' => $site,
        'title' => 'Not found',
        'body' => '<p class="muted">That page does not exist.</p>',
    ]);
}
