<?php
namespace App\Services\Articles;
use App\Models\Article;
class ArticleQualityGate { private $duplicateResolver; public function __construct(?callable $duplicateResolver=null){$this->duplicateResolver=$duplicateResolver;}
 public function check(Article $a, ?array $safety=null): array {
  $reasons=[];$score=0;$title=trim((string)$a->title);$excerpt=trim((string)$a->excerpt);$content=trim(strip_tags((string)$a->content));$source=trim((string)($a->source_content?:$a->content));$fp=(string)$a->fingerprint;$tfp=hash('sha256',preg_replace('/\\s+/',' ',mb_strtolower($title)));$dup=$this->duplicateResolver ? (bool)($this->duplicateResolver)($a,$fp,$tfp) : Article::where(function($q)use($fp,$tfp){$q->where('fingerprint',$fp)->orWhere('title_fingerprint',$tfp);})->whereKeyNot($a->id)->exists();
  if($title!=='')$score+=10;else $reasons[]='empty_title';
  if($excerpt!=='')$score+=10;else $reasons[]='empty_excerpt';
  if(mb_strlen($content)>=500)$score+=20;elseif(mb_strlen($content)>=250)$score+=10;else $reasons[]='content_too_short';
  if(mb_strlen($source)>=(int)config('berita.news_min_source_chars',1000))$score+=15;else $reasons[]='source_too_thin';
  if(filter_var($a->image_url,FILTER_VALIDATE_URL))$score+=10;else $reasons[]='invalid_image';
  if($a->publisher&&$a->category_id&&filter_var($a->source_url,FILTER_VALIDATE_URL)&&$a->source_published_at)$score+=10;else $reasons[]='metadata_incomplete'; if(!$dup)$score+=10;else $reasons[]='duplicate';
  if(!$this->badContent($a->content))$score+=15;else $reasons[]='invalid_content';
  if($safety!==null&&($safety['pass']??false))$score+=15;elseif($safety!==null)$reasons=array_merge($reasons,$safety['reasons']??['safety_failed']);
  $threshold=(int)config('berita.news_min_quality_score',75);return ['pass'=>$score>=$threshold&&$reasons===[],'score'=>$score,'reasons'=>$reasons];
 }
 private function badContent(string $v): bool {$t=trim($v);return $t===''||str_contains($t,'title')&&str_contains($t,'content')||(bool)preg_match('/<[^>]+>/',$t);}
}