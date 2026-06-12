# php-forge

Dump raw material in one end — JSON, HTML, CSS, images — get pages out the other. Small PHP router + templates; no build step.

This repo powers [Blackheart Tech Forge](content/site.json) (`content/` is the site data; `app/` is the engine).

## Layout

```text
public/index.php          # web entry
app/                      # routing, templates
content/
  site.json               # site title + tagline
  style.css
  home.html
  contact.html
  projects/*.json
  updates/*.json
  img/                    # optional static images
```

## Site config (`content/site.json`)

`title`, `tagline`, `description`, `keywords` (site-wide meta keywords), `url` (primary domain for canonical/OG/sitemap — use one even if several point here), `footer`, optional `logo`, `logoAlt`, `ogImage`.

Per-entry optional: `description` (meta override), `ogImage`, `keywords` (extra meta keywords merged with `tags`).

Replace `content/img/logo.svg` with your own mark; PNG/WebP also work.

## JSON fields

`title`, `date`, `stub`, `tags`, `images`, `content` (HTML)

Optional on updates: `project` (slug linking to a project).

Optional on projects: `progress` (0–100), `finished` and `todo` (string arrays).

URL: `/projects/{slug}` → `content/projects/{slug}.json`

## Run locally

```bash
cd public
php -S localhost:8080 index.php
```

Open http://localhost:8080

Apache: point document root at `public/` (`.htaccess` included).

## Routes

| Path | Source |
|------|--------|
| `/` | `content/home.html` |
| `/contact` | `content/contact.html` |
| `/projects` | list `content/projects/*.json` |
| `/projects/{slug}` | one JSON file |
| `/updates` | list `content/updates/*.json` |
| `/updates/{slug}` | one JSON file |
| `/search?q=…` | search JSON bodies |
| `/style.css`, `/img/…` | served from `content/` |
