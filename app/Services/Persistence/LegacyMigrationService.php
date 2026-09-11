<?php

namespace App\Services\Persistence;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LegacyMigrationService
{
    public function import(string $path, bool $dryRun = false): array
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException("Legacy file not found: {$path}");
        }

        $payload = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $rows = array_is_list($payload) ? $payload : ($payload['articles'] ?? []);
        if (!is_array($rows)) {
            throw new \InvalidArgumentException('Legacy JSON must contain an article array.');
        }

        $report = [
            'path' => $path,
            'total' => count($rows),
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'failed' => 0,
            'dry_run' => $dryRun,
            'errors' => [],
        ];

        foreach ($rows as $index => $row) {
            try {
                if (!is_array($row)) {
                    throw new \InvalidArgumentException('Row is not an object.');
                }

                $title = trim((string) ($row['title'] ?? ''));
                $url = trim((string) ($row['sourceUrl'] ?? $row['source_url'] ?? $row['url'] ?? ''));
                if ($title === '' || $url === '') {
                    $report['skipped']++;
                    continue;
                }

                $fingerprint = trim((string) ($row['fingerprint'] ?? '')) ?: hash('sha256', Str::lower($url));
                $slug = trim((string) ($row['slug'] ?? '')) ?: Str::slug($title) . '-' . substr($fingerprint, 0, 8);
                $categoryName = trim((string) ($row['category'] ?? $row['category_name'] ?? 'Nasional')) ?: 'Nasional';

                $attributes = ['fingerprint' => $fingerprint];
                $values = [
                    'slug' => $slug,
                    'title' => $title,
                    'content' => $row['content'] ?? $row['body'] ?? $row['excerpt'] ?? $title,
                    'excerpt' => $row['excerpt'] ?? null,
                    'source_id' => $row['sourceId'] ?? $row['source_id'] ?? null,
                    'publisher' => $row['publisher'] ?? null,
                    'source_name' => $row['sourceName'] ?? $row['source_name'] ?? null,
                    'source_url' => $url,
                    'canonical_url' => $row['canonicalUrl'] ?? $row['canonical_url'] ?? $url,
                    'fingerprint' => $fingerprint,
                    'title_fingerprint' => $row['titleFingerprint'] ?? hash('sha256', Str::lower(preg_replace('/\s+/', ' ', $title))),
                    'language' => $row['language'] ?? 'id',
                    'image_url' => $row['imageUrl'] ?? $row['image_url'] ?? null,
                    'image_alt' => $row['imageAlt'] ?? $row['image_alt'] ?? null,
                    'source_published_at' => $row['sourcePublishedAt'] ?? $row['source_published_at'] ?? $row['publishedAt'] ?? null,
                    'site_published_at' => $row['sitePublishedAt'] ?? $row['site_published_at'] ?? $row['publishedAt'] ?? null,
                    'updated_at_content' => $row['updatedAt'] ?? $row['updated_at_content'] ?? null,
                    'generation_status' => $row['generationStatus'] ?? $row['generation_status'] ?? 'published',
                    'metadata' => $row['metadata'] ?? $row,
                ];

                if ($dryRun) {
                    $report[Article::where('fingerprint', $fingerprint)->exists() ? 'updated' : 'imported']++;
                    continue;
                }

                DB::transaction(function () use ($attributes, $values, $categoryName, &$report): void {
                    $category = Category::firstOrCreate(['name' => $categoryName]);
                    $values['category_id'] = $category->id;
                    $article = Article::where($attributes)->first();
                    if ($article) {
                        $article->fill($values)->save();
                        $report['updated']++;
                    } else {
                        Article::create($values);
                        $report['imported']++;
                    }
                });
            } catch (\Throwable $e) {
                $report['failed']++;
                $report['errors'][] = ['row' => $index, 'message' => $e->getMessage()];
            }
        }

        return $report;
    }
}
