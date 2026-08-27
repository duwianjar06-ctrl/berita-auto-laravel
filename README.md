# Berita Auto Laravel

Laravel migration of Berita Auto for MySQL + Blade + cPanel/Rumahweb shared hosting.

## Runtime safety

The migration defaults to safe staging mode:

```env
AUTOMATION_ENABLED=false
INSTAGRAM_PREPARATION_ENABLED=false
INSTAGRAM_AUTO_PUBLISH=false
```

The legacy Next.js/Vercel production application is not modified by this repository.

## Architecture

- Laravel 12 / PHP 8.2+
- MySQL persistence
- Blade + Tailwind utility classes + Alpine.js
- Laravel Scheduler + cPanel cron
- Database queue with `--stop-when-empty`
- Google OAuth admin gate with `ADMIN_EMAILS`
- MySQL-backed Instagram READY/publish state
- FTPS deployment from GitHub Actions
- No Node.js runtime required on production hosting

## Important docs

- `docs/migration-audit.md`
- `docs/feature-parity.md`
- `docs/data-migration.md`
- `docs/rumahweb-deployment.md`

## Legacy source discrepancy

The requested source `duwianjar06-ctrl/berita-auto` `main` branch currently contains a reduced/restored package state while its own `SKILL.md` identifies `feature/auto-news-mvp` as the application/production branch. The audit documents this discrepancy. The feature branch was read as supplementary behavior evidence only; no source write was performed.
