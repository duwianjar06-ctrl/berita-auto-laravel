# Migration audit

## Source verification
- Requested source: `duwianjar06-ctrl/berita-auto`, `main`.
- `main` currently contains only the restored package/worker shell (`app`, `lib`, `worker`, package/config metadata); the repository's own `SKILL.md` states that the production application branch is `feature/auto-news-mvp`.
- Because the requested `main` branch does not contain the production UI/API/data implementation described by its own architecture documentation, the feature branch was read as supplementary source evidence only. No source repository was modified.

## Mapping
| FEATURE | SOURCE FILE / EVIDENCE | CURRENT BEHAVIOR | CURRENT STORAGE | LARAVEL EQUIVALENT | STATUS |
|---|---|---|---|---|---|
| Public homepage | `app` + `SKILL.md` | Published articles, categories, dense newsroom UI | JSON/Redis | Blade `home` + `Article` | MIGRATED |
| Article detail | `SKILL.md` | Full body, canonical/SEO/source attribution | JSON/Redis | Blade `article` | MIGRATED |
| Categories | `lib/categories.js` / `SKILL.md` | 12 named categories | JSON | `categories` + Eloquent | MIGRATED |
| RSS ingestion | `lib/sources.js`, `lib/rss.js` / `SKILL.md` | Official RSS/Atom, source isolation, 7s timeout | pending queue | `NewsIngestionService` + MySQL | MIGRATED |
| Dedupe | `worker/normalize.js` / `SKILL.md` | URL/title fingerprints | pending/published | unique fingerprints in `articles` | MIGRATED |
| Article generation | `lib/ai-providers.js` / `lib/ai.js` | Gemini primary, optional OpenAI, factual fallback | article store | `ArticleGenerationService` | MIGRATED WITH SIMPLIFICATION |
| Image validation | `worker/social-preparation.js` / `SKILL.md` | Candidate requires usable image | article/social state | image URL validation/discovery | IN PROGRESS |
| Admin Berita | `app/admin-berita` / `SKILL.md` | Filters, distribution, notes, automation monitoring | JSON/Redis | Blade admin + MySQL | MIGRATED WITH UI SIMPLIFICATION |
| Instagram preparation | `admin-instagram`, `worker/social-run.js`, `lib/social-preparation.js` | bounded READY hysteresis | Redis | `InstagramPreparation` + `instagram_posts` | MIGRATED |
| Instagram review/READY | `SKILL.md` | durable READY handoff | Redis | `instagram_posts`, queue | MIGRATED |
| Instagram publisher | `lib/instagram.js`, `worker/social-run.js` | container/status/publish, throttle, quota, idempotency | Redis | `PublisherService` + MySQL | MIGRATED WITH SAFETY SIMPLIFICATION |
| Scheduler | source `SKILL.md` | QStash primary, GitHub fallback | external | Laravel Scheduler/cPanel cron | MIGRATED |
| Queue | source `SKILL.md` | external/worker based | Redis | database queue | MIGRATED |
| Analytics | source `SKILL.md` | optional Upstash Redis analytics | Upstash | NOT YET PORTED | NOT STARTED |
| Search Console | source branch UI/docs | admin search analytics/import | JSON/Redis | NOT YET PORTED | NOT STARTED |
| Ads | source `SKILL.md` | reusable ad slots/WhatsApp CTA | code | NOT YET PORTED | NOT STARTED |
| SEO | `SKILL.md` | metadata, NewsArticle JSON-LD, sitemap, robots | code | Blade metadata + sitemap/robots | MIGRATED |
| OAuth admin | `auth.js`, `SKILL.md` | Google OAuth + `ADMIN_EMAILS` | session | manual Google OAuth flow | MIGRATED |
| Legacy JSON import | source `lib/storage.js`, data docs | persistent JSON snapshots | Git | `berita:migrate-legacy` | MIGRATED |

## Safety invariant
`AUTOMATION_ENABLED`, `INSTAGRAM_PREPARATION_ENABLED`, and `INSTAGRAM_AUTO_PUBLISH` are false by default. Laravel therefore cannot publish Instagram content unless explicitly enabled in a staging/production environment.
