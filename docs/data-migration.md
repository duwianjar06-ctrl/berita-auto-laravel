# Legacy data migration

The source repository's production branch currently does not expose the legacy JSON data files in `main`. The Laravel import command is therefore intentionally input-driven and non-destructive.

## Command

```bash
php artisan berita:migrate-legacy data/articles.json --dry-run
php artisan berita:migrate-legacy data/articles.json
```

The importer:
- accepts a JSON array of legacy article records;
- uses the existing fingerprint when present, otherwise SHA-256 of the source URL;
- skips existing fingerprints;
- preserves source metadata, timestamps, image URL and legacy fields in `articles.metadata`;
- can be rerun safely;
- performs no deletes or updates to existing articles;
- reports total, inserted, skipped and invalid records.

Run the dry-run first. Export/copy the legacy JSON from the source environment into staging through an approved file transfer; do not commit production secrets or credentials.

Pending candidates should only be imported after a separate review because pending is operational queue state, not public content.
