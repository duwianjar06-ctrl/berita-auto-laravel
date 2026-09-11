# Berita Auto migration audit

## Scope

Reference: `duwianjar06-ctrl/berita-auto` (`main`)
Target: `duwianjar06-ctrl/berita-auto-laravel` (`main`)

## Source findings

The legacy repository is a Next.js 15 application with public/admin route code under `app/`, API handlers under `app/api/`, shared behavior under `lib/`, and scheduled news/social workers under `worker/`. The source package scripts expose public, article, routing, sitemap, admin UI, Instagram, image, and automation regression suites.

The repository tree does not itself prove production data availability. Production persistence must be exported separately from the actual configured provider (for example Redis/Upstash, Vercel Blob, or another API), without copying secrets into Git.

## Target mapping

| Legacy feature | Laravel target | Status |
|---|---|---|
| `/` public homepage | `PublicController@home`, `resources/views/home.blade.php` | Existing route/view; visual parity still pending |
| Article detail and legacy `/berita/{slug}` | `PublicController@article`, `article.blade.php` | Route compatibility present |
| Category route | `PublicController@category`, `category.blade.php` | Present; identifier normalization should be verified against exported data |
| Sitemap and robots | `PublicController@sitemap`, `robots` | Present |
| Admin news | `AdminController`, `resources/views/admin` | Existing Laravel surface; needs visual/behavioral parity review |
| Admin Instagram | `InstagramController`, Instagram models/views | Existing guarded surface; automation intentionally not enabled by this migration |
| News automation | Laravel console commands/services | Existing foundation; no production execution performed |
| Legacy article persistence | `LegacyMigrationService`, `MigrateLegacy` | Hardened in this change |

## Import contract

Command:

```bash
php artisan berita-auto:import /path/to/export.json --dry-run
php artisan berita-auto:import /path/to/export.json
```

Accepted top-level JSON forms:

- an array of article objects;
- an object containing an `articles` array.

The importer accepts both camelCase and snake_case legacy keys, preserves the stable fingerprint when present, derives one from the source URL otherwise, creates missing categories, updates existing articles by fingerprint, and never performs destructive deletes. Invalid rows are counted as skipped; exceptions are counted as failed and returned in the report.

## Data safety

- No production database was accessed or modified.
- No `.env`, token, password, session, or lock data is stored.
- No `migrate:fresh`, `db:wipe`, table deletion, production import, or deployment was run.
- `scripts/deploy/smart-deploy.sh` and deployment workflow were not changed.

## Remaining verification

Before production import, obtain a sanitized export from the actual legacy persistence provider and run the dry-run importer in a staging/local Laravel environment. Compare source and target totals, category/source counts, duplicate slug/fingerprint counts, newest article, publication timestamps, image URLs, and route responses. Visual parity requires browser screenshots or a running preview of both applications; it cannot be truthfully marked complete from repository source alone.
