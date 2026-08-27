# Feature parity

Statuses are based on the source behavior actually inspected and the Laravel implementation currently committed.

- [x] Public homepage — MIGRATED
- [x] `/artikel/{slug}` and `/berita/{slug}` — MIGRATED
- [x] Categories and published-only filtering — MIGRATED
- [x] RSS/Atom source registry and source-isolated ingestion — MIGRATED
- [x] URL/title duplicate prevention — MIGRATED
- [x] MySQL article/category/source persistence — MIGRATED
- [x] Legacy article JSON dry-run/idempotent import command — MIGRATED
- [x] Admin Berita list/search/source filtering — MIGRATED
- [x] Google OAuth admin gate using `ADMIN_EMAILS` — MIGRATED
- [x] Instagram preparation with durable progress and READY state — MIGRATED
- [x] Instagram queue/history/attention pages — MIGRATED
- [x] Instagram publisher container/status/publish persistence — MIGRATED WITH SAFETY SIMPLIFICATION
- [x] Persistent locking and non-overlap protection — MIGRATED
- [x] Database queue and stop-when-empty scheduler pattern — MIGRATED
- [x] cPanel-compatible `schedule:run` deployment model — MIGRATED
- [x] Daily log rotation — MIGRATED
- [x] Health/status endpoint and page — MIGRATED
- [x] robots.txt and sitemap — MIGRATED
- [x] `.env.example` with automation-off defaults — MIGRATED
- [x] GitHub Actions CI/deployment skeleton — MIGRATED
- [ ] Full source visual parity for the dense newsroom/admin UI — IN PROGRESS
- [ ] Upstash analytics migration — NOT STARTED
- [ ] Search Console analytics/import parity — NOT STARTED
- [ ] Full ad slot parity — NOT STARTED
- [ ] Full carousel/card rendering parity — NOT STARTED
- [ ] Full Instagram carousel/reconciliation parity — IN PROGRESS
- [ ] Live Meta quota/retry verification — NOT STARTED (must remain OFF)
- [ ] Legacy data import execution — NOT STARTED until legacy JSON is supplied to staging

## Intentional changes
1. Redis/QStash are removed from the production runtime in favor of MySQL + Laravel Scheduler + database queue.
2. Instagram auto-publish remains disabled by default and ambiguous publish outcomes are held for reconciliation rather than blindly retried, to avoid double-posting.
3. Frontend is Blade-first and uses no Node runtime in production; CI keeps a no-op Node build step because the rendered frontend does not require a JS build artifact yet.
