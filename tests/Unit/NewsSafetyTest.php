<?php
namespace Tests\Unit;
use Tests\TestCase;
use App\Models\Article;
use App\Services\Articles\ArticleGenerationService;
use App\Services\News\NewsIngestionService;
class NewsSafetyTest extends TestCase {
 private function invoke(object $o,string $m,array $args=[]):mixed{$r=new \ReflectionMethod($o,$m);$r->setAccessible(true);return $r->invokeArgs($o,$args);}
 public function test_generation_fallback_uses_full_source():void{config(['berita.gemini_api_key'=>null]);$a=new Article(['title'=>'Judul','excerpt'=>'ringkas','source_content'=>str_repeat('Fakta sumber. ',100)]);$g=$this->invoke(app(ArticleGenerationService::class),'generate',[$a]);$this->assertSame(str_repeat('Fakta sumber. ',100),$a->source_content);$this->assertNotEmpty($g['data']['content']);}
 public function test_unsupported_number_is_detected():void{$a=new Article(['title'=>'Judul','excerpt'=>'','source_content'=>'Harga Rp 10 juta.']);$g=$this->invoke(app(ArticleGenerationService::class),'safe',[['title'=>'Judul','excerpt'=>'','content'=>'Harga Rp 20 juta.'],$a]);$this->assertFalse($g['pass']);$this->assertStringContainsString('unsupported_number',$g['reasons'][0]);}
 public function test_excessive_overlap_is_detected():void{$source=str_repeat('Kalimat yang sama dari sumber berita ini sangat panjang untuk pengujian overlap. ',10);$a=new Article(['source_content'=>$source]);$g=$this->invoke(app(ArticleGenerationService::class),'safe',[['title'=>'Judul','excerpt'=>'','content'=>$source],$a]);$this->assertContains('excessive_overlap',$g['reasons']);}
 public function test_canonical_url_removes_tracking_parameters():void{$s=app(NewsIngestionService::class);$v=$this->invoke($s,'canonicalUrl',['HTTPS://Example.COM/story/?utm_source=x&fbclid=y&id=7']);$this->assertSame('https://example.com/story?id=7',$v);}
 public function test_generation_attempt_limit_is_configured():void{$this->assertSame(3,(int)config('berita.news_max_generation_attempts'));}
}
