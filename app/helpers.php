<?php

declare(strict_types=1);

function render(string $template, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    require TEMPLATES_DIR . '/' . $template . '.php';
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function request_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $path = is_string($path) ? $path : '/';
    $path = '/' . trim($path, '/');

    return $path === '/' ? '/' : rtrim($path, '/');
}

function project_progress(array $data): ?int
{
    if (!isset($data['progress']) || !is_numeric($data['progress'])) {
        return null;
    }

    return max(0, min(100, (int) $data['progress']));
}

/** @return list<string> */
function project_list_items(array $data, string $key): array
{
    if (empty($data[$key]) || !is_array($data[$key])) {
        return [];
    }

    $items = [];
    foreach ($data[$key] as $item) {
        if (is_string($item) && $item !== '') {
            $items[] = $item;
        }
    }

    return $items;
}

function nav_is_active(string $href): bool
{
    $path = request_path();

    if ($href === '/') {
        return $path === '/';
    }

    return $path === $href || str_starts_with($path, $href . '/');
}

function site_logo(array $site): ?string
{
    if (empty($site['logo']) || !is_string($site['logo'])) {
        return null;
    }

    return $site['logo'];
}

function site_logo_alt(array $site): string
{
    if (!empty($site['logoAlt']) && is_string($site['logoAlt'])) {
        return $site['logoAlt'];
    }

    return (string) ($site['title'] ?? 'Site');
}

function site_config(): array
{
    static $config = null;

    if ($config !== null) {
        return $config;
    }

    $path = CONTENT_DIR . '/site.json';
    if (!is_file($path)) {
        $config = ['title' => 'Site', 'tagline' => ''];
        return $config;
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    $config = is_array($decoded) ? $decoded : ['title' => 'Site', 'tagline' => ''];

    return $config;
}
