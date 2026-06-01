<?php

declare(strict_types=1);

function load_json_file(string $path): ?array
{
    if (!is_file($path)) {
        return null;
    }

    $decoded = json_decode((string) file_get_contents($path), true);

    return is_array($decoded) ? $decoded : null;
}

function collection_dir(string $name): string
{
    return CONTENT_DIR . '/' . $name;
}

/** @return list<array{slug: string, path: string, data: array}> */
function list_entries(string $collection): array
{
    $dir = collection_dir($collection);
    if (!is_dir($dir)) {
        return [];
    }

    $entries = [];
    foreach (glob($dir . '/*.json') ?: [] as $path) {
        $data = load_json_file($path);
        if ($data === null) {
            continue;
        }

        $entries[] = [
            'slug' => basename($path, '.json'),
            'path' => $path,
            'data' => $data,
        ];
    }

    usort($entries, static function (array $a, array $b): int {
        $dateA = $a['data']['date'] ?? $a['slug'];
        $dateB = $b['data']['date'] ?? $b['slug'];

        return strcmp((string) $dateB, (string) $dateA);
    });

    return $entries;
}

function find_entry(string $collection, string $slug): ?array
{
    $path = collection_dir($collection) . '/' . $slug . '.json';
    $data = load_json_file($path);
    if ($data === null) {
        return null;
    }

    return [
        'slug' => $slug,
        'path' => $path,
        'data' => $data,
    ];
}

function update_project_slug(array $data): ?string
{
    if (empty($data['project']) || !is_string($data['project'])) {
        return null;
    }

    return $data['project'];
}

/** @return list<array{slug: string, path: string, data: array}> */
function updates_for_project(string $projectSlug): array
{
    $linked = [];
    foreach (list_entries('updates') as $entry) {
        if (update_project_slug($entry['data']) === $projectSlug) {
            $linked[] = $entry;
        }
    }

    return $linked;
}

/** @return list<array{slug: string, path: string, data: array}> */
function projects_for_update(array $data): array
{
    $slug = update_project_slug($data);
    if ($slug === null) {
        return [];
    }

    $entry = find_entry('projects', $slug);

    return $entry !== null ? [$entry] : [];
}

function load_page_fragment(string $name): ?string
{
    $path = CONTENT_DIR . '/' . $name . '.html';
    if (!is_file($path)) {
        return null;
    }

    return (string) file_get_contents($path);
}

/** @return list<array{collection: string, slug: string, data: array}> */
function search_entries(string $query): array
{
    $query = trim($query);
    if ($query === '') {
        return [];
    }

    $needle = mb_strtolower($query);
    $hits = [];

    foreach (['projects', 'updates'] as $collection) {
        foreach (list_entries($collection) as $entry) {
            $data = $entry['data'];
            $haystack = mb_strtolower(implode(' ', [
                $data['title'] ?? '',
                $data['stub'] ?? '',
                implode(' ', $data['tags'] ?? []),
                strip_tags((string) ($data['content'] ?? '')),
            ]));

            if (str_contains($haystack, $needle)) {
                $hits[] = [
                    'collection' => $collection,
                    'slug' => $entry['slug'],
                    'data' => $data,
                ];
            }
        }
    }

    return $hits;
}

function serve_static_if_exists(string $path): bool
{
    if ($path === '/style.css') {
        $file = CONTENT_DIR . '/style.css';
    } elseif (preg_match('#^/img/(.+)$#', $path, $matches)) {
        $file = CONTENT_DIR . '/img/' . $matches[1];
    } else {
        return false;
    }

    if (!is_file($file)) {
        http_response_code(404);
        echo 'Not found';
        return true;
    }

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $types = [
        'css' => 'text/css; charset=utf-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
    ];

    header('Content-Type: ' . ($types[$ext] ?? 'application/octet-stream'));
    readfile($file);

    return true;
}
