<?php
namespace App\Services\Articles;
use App\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
class ArticleGenerationService {
 public function publishNext(): ?Article {
  $article=Article::where('generation_status','pending')->where('source_published_at','>=',now()->subHours((int)config('berita.freshness_hours',12)))->orderByDesc('source_published_at')->first(); if(!$article)return null;
  if(!$article->image_url) $article->image_url=$this->discoverImage($article->source_url);
  if(!$article->image_url){$article->generation_status='rejected';$article->save();return null;}
  $generated=$this->generate($article); $article->title=$generated['title'];$article->content=$generated['content'];$article->excerpt=$generated['excerpt'];$article->generation_status='published';$article->site_published_at=now();$article->updated_at_content=now();$article->save(); return $article;
 }
 private function generate(Article $a):array { $fallback=['title'=>$a->title,'content'=>$a->content,'excerpt'=>$a->excerpt]; $key=config('berita.gemini_api_key'); if(!$key)return $fallback; try{$model=config('berita.gemini_model','gemini-2.5-flash-lite');$url='https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent?key='.urlencode($key);$prompt='Tulis ulang berita berikut dalam Bahasa Indonesia secara faktual. Jangan mengarang fakta, kutipan, angka, nama, URL, atau kejadian. Kembalikan JSON dengan title, excerpt, content. Judul: '.$a->title.' Sumber: '.$a->content; $r=Http::timeout(18)->post($url,['contents'=>[['parts'=>[['text'=>$prompt]]]],'generationConfig'=>['responseMimeType'=>'application/json']]); if(!$r->successful())return $fallback; $text=$r->json('candidates.0.content.parts.0.text');$json=json_decode($text,true); if(!is_array($json)||empty($json['content']))return $fallback; return array_merge($fallback,$json);}catch(\Throwable){return $fallback;} }
 private function discoverImage(?string $url):?string { if(!$url)return null; try{$r=Http::timeout(5)->get($url); if(!$r->successful())return null; preg_match('/<meta[^>]+property=["\\\']og:image["\\\'][^>]+content=["\\\']([^"\\\']+)/i',$r->body(),$m); return isset($m[1])&&filter_var($m[1],FILTER_VALIDATE_URL)?$m[1]:null;}catch(\Throwable){return null;} }
}
