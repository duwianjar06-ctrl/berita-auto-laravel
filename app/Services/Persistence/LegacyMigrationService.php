<?php
namespace App\Services\Persistence;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Str;

class LegacyMigrationService
{
    public function import(string $path, bool $dryRun = false): array
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException("Legacy file not found: {$path}");
        }
        $rows = json_decode(file_get_contents($path), true);
        $rows = is_array($rows) ? $rows : [];
        $report = ['path'=>$path,'total'=>count($rows),'inserted'=>0,'skipped'=>0,'invalid'=>0,'dry_run'=>$dryRun];
        foreach ($rows as $row) {
            $title = trim((string)($row['title'] ?? ''));
            $url = trim((string)($row['sourceUrl'] ?? $row['source_url'] ?? $row['url'] ?? ''));
            if ($title === '' || $url === '') { $report['invalid']++; continue; }
            $fingerprint = (string)($row['fingerprint'] ?? hash('sha256', Str::lower($url)));
            if (Article::where('fingerprint', $fingerprint)->exists()) { $report['skipped']++; continue; }
            if (!$dryRun) {
                $category = Category::firstOrCreate(['name'=>(string)($row['category'] ?? 'Nasional')]);
                Article::create([
                    'slug'=>$row['slug'] ?? Str::slug($title).'-'.substr($fingerprint,0,8),
                    'title'=>$title,
                    'content'=>$row['content'] ?? $row['body'] ?? $row['excerpt'] ?? $title,
                    'excerpt'=>$row['excerpt'] ?? null,
                    'category_id'=>$category->id,
                    'source_id'=>$row['sourceId'] ?? $row['source_id'] ?? null,
                    'publisher'=>$row['publisher'] ?? null,
                    'source_name'=>$row['sourceName'] ?? $row['source_name'] ?? null,
                    'source_url'=>$url,
                    'canonical_url'=>$row['canonicalUrl'] ?? $url,
                    'fingerprint'=>$fingerprint,
                    'title_fingerprint'=>$row['titleFingerprint'] ?? hash('sha256', Str::lower(preg_replace('/\s+/', ' ', $title))),
                    'language'=>$row['language'] ?? 'id',
                    'image_url'=>$row['imageUrl'] ?? $row['image_url'] ?? null,
                    'image_alt'=>$row['imageAlt'] ?? null,
                    'source_published_at'=>$row['sourcePublishedAt'] ?? $row['publishedAt'] ?? null,
                    'site_published_at'=>$row['sitePublishedAt'] ?? $row['publishedAt'] ?? null,
                    'updated_at_content'=>$row['updatedAt'] ?? null,
                    'generation_status'=>'published',
                    'metadata'=>$row,
                ]);
            }
            $report['inserted']++;
        }
        return $report;
    }
}
