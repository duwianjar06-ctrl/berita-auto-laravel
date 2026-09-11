<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LegacyMigrationTest extends TestCase
{
    use RefreshDatabase;

    private function fixturePath(): string
    {
        return base_path('tests/Fixtures/legacy-export-sample.json');
    }

    public function test_dry_run_does_not_insert_articles(): void
    {
        $exitCode = Artisan::call('berita-auto:import', ['path' => $this->fixturePath(), '--dry-run' => true]);

        $this->assertSame(0, $exitCode);
        $this->assertDatabaseCount('articles', 0);
    }

    public function test_import_is_idempotent_and_preserves_legacy_fields(): void
    {
        $this->assertSame(0, Artisan::call('berita-auto:import', ['path' => $this->fixturePath()]));
        $this->assertDatabaseCount('articles', 1);
        $this->assertDatabaseHas('articles', [
            'slug' => 'berita-auto-fixture',
            'source_url' => 'https://example.com/berita-auto-fixture',
            'image_url' => 'https://example.com/fixture.jpg',
            'fingerprint' => 'fixture-fingerprint-001',
        ]);

        $this->assertSame(0, Artisan::call('berita-auto:import', ['path' => $this->fixturePath()]));
        $this->assertDatabaseCount('articles', 1);
        $this->assertSame(1, Article::where('fingerprint', 'fixture-fingerprint-001')->count());
    }
}
