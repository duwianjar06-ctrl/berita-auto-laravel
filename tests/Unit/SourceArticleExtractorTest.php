<?php
namespace Tests\Unit;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\Articles\SourceArticleExtractor;
class SourceArticleExtractorTest extends TestCase{
 public function test_json_ld_is_extracted():void{Http::fake(['https://example.test/*'=>Http::response('<script type="application/ld+json">{"articleBody":"'.str_repeat('Berita lengkap. ',100).' "}</script>',200,['Content-Type'=>'text/html'])]);$r=app(SourceArticleExtractor::class)->extract('https://example.test/a','ringkas');$this->assertSame('json_ld',$r['method']);$this->assertGreaterThan(1000,$r['char_count']);}
 public function test_article_is_extracted():void{Http::fake(['https://example.test/*'=>Http::response('<nav>x</nav><article>'.str_repeat('Isi artikel. ',120).'</article>',200,['Content-Type'=>'text/html'])]);$r=app(SourceArticleExtractor::class)->extract('https://example.test/a');$this->assertSame('article',$r['method']);$this->assertStringNotContainsString('x',$r['text']);}
 public function test_main_and_rss_fallback():void{Http::fake(['https://example.test/main'=>Http::response('<main>'.str_repeat('Isi utama. ',100).'</main>',200,['Content-Type'=>'text/html']),'https://example.test/bad'=>Http::response('<html><body>tipis</body></html>',200,['Content-Type'=>'text/html'])]);$this->assertSame('main',app(SourceArticleExtractor::class)->extract('https://example.test/main')['method']);$this->assertSame('rss_fallback',app(SourceArticleExtractor::class)->extract('https://example.test/bad',str_repeat('Ringkasan cukup. ',30))['method']);}
 public function test_invalid_url_does_not_fetch():void{$r=app(SourceArticleExtractor::class)->extract('ftp://example.test/a','fallback');$this->assertSame('invalid_url',$r['method']);}
}
