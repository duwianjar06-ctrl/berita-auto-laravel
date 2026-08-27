<?php
namespace App\Services\News;
use App\Models\{NewsSource,Article,Category};
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
class NewsIngestionService {
 public function run(): array {
  if(!config('berita.automation_enabled')) return ['status'=>'disabled','processed'=>0,'added'=>0];
  $added=0;$processed=0;$failures=0;$now=now();
  foreach(NewsSource::where('enabled',true)->get() as $source){
   $processed++; try{$response=Http::timeout(7)->get($source->feed_url); if(!$response->successful()) throw new \RuntimeException('HTTP '.$response->status()); $xml=@simplexml_load_string($response->body()); if(!$xml) throw new \RuntimeException('Malformed RSS/Atom'); $items=$this->items($xml); foreach($items as $item){ if(!$item['url']||!filter_var($item['url'],FILTER_VALIDATE_URL)) continue; $fp=hash('sha256',Str::lower(trim($item['url']))); if(Article::where('fingerprint',$fp)->exists()) continue; $titleFp=hash('sha256',preg_replace('/\\s+/',' ',Str::lower(trim($item['title'])))); if(Article::where('title_fingerprint',$titleFp)->exists()) continue; $category=Category::firstOrCreate(['name'=>$this->category($source->website_category,$item['title'])]); Article::create(['slug'=>Str::slug($item['title']).'-'.substr($fp,0,8),'title'=>$item['title'],'content'=>$item['summary']?:$item['title'],'excerpt'=>$item['summary'],'category_id'=>$category->id,'source_id'=>$source->source_id,'publisher'=>$source->publisher,'source_name'=>$source->name,'source_url'=>$item['url'],'canonical_url'=>$item['url'],'fingerprint'=>$fp,'title_fingerprint'=>$titleFp,'language'=>$source->language,'source_published_at'=>$item['published'],'generation_status'=>'pending']); $added++; } $source->update(['last_success_at'=>$now,'last_error'=>null]); }catch(\Throwable $e){$failures++;$source->update(['last_failure_at'=>$now,'last_error'=>Str::limit($e->getMessage(),500)]);}
  }
  return ['status'=>'ok','processed'=>$processed,'added'=>$added,'failures'=>$failures];
 }
 private function items(\SimpleXMLElement $xml):array { $out=[]; $nodes=isset($xml->channel->item)?$xml->channel->item:$xml->entry; foreach($nodes as $n){$title=trim((string)($n->title??''));$url=trim((string)($n->link??'')); if(!$url && isset($n->link['href']))$url=(string)$n->link['href'];$summary=trim((string)($n->description??$n->summary??''));$date=(string)($n->pubDate??$n->published??$n->updated??'');$out[]=['title'=>$title,'url'=>$url,'summary'=>strip_tags($summary),'published'=>$date&&strtotime($date)?date('Y-m-d H:i:s',strtotime($date)):null];} return $out; }
 private function category(?string $hint,string $title):string { $allowed=['Nasional','Internasional','Ekonomi','Bisnis','Teknologi','Olahraga','Hiburan','Lifestyle','Otomotif','Sains','Politik','Daerah']; foreach($allowed as $c) if(Str::lower((string)$hint)==Str::lower($c)) return $c; $map=['teknologi'=>'Teknologi','sport'=>'Olahraga','olahraga'=>'Olahraga','ekonomi'=>'Ekonomi','bisnis'=>'Bisnis','politik'=>'Politik','otomotif'=>'Otomotif','sains'=>'Sains','hiburan'=>'Hiburan','lifestyle'=>'Lifestyle','daerah'=>'Daerah','internasional'=>'Internasional']; foreach($map as $k=>$c) if(Str::contains(Str::lower($title),$k)) return $c; return 'Nasional'; }
}
