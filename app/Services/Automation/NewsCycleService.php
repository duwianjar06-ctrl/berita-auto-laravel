<?php
namespace App\Services\Automation;
use App\Models\{AutomationRun,AutomationLock};
use App\Services\News\NewsIngestionService;
use App\Services\Articles\ArticleGenerationService;
use Illuminate\Support\Str;
class NewsCycleService {
 public function run():array{
  if(!config('berita.automation_enabled'))return ['status'=>'disabled'];
  $lock=AutomationLock::firstOrCreate(['name'=>'news_cycle']);
  if($lock->expires_at&&$lock->expires_at->isFuture())return ['status'=>'locked'];
  $owner=(string)Str::uuid();$lock->update(['owner'=>$owner,'expires_at'=>now()->addMinutes(10)]);
  $started=microtime(true);$run=null;$t=array_fill_keys(['sources_checked','sources_failed','rss_items_seen','new_candidates','duplicates_skipped','candidates_scanned','source_fetch_success','source_fetch_failed','generated_ai','generated_fallback','generation_failed','quality_pass','quality_failed','published','rejected','deferred'],0);$err=null;
  try{
   $run=AutomationRun::create(['run_uuid'=>$owner,'type'=>'news_cycle','trigger'=>'scheduler','status'=>'running','target'=>(int)config('berita.news_publish_target',2),'started_at'=>now(),'telemetry'=>[]]);
   $ing=app(NewsIngestionService::class)->run();foreach(['sources_checked','sources_failed','rss_items_seen','new_candidates','duplicates_skipped'] as $k)$t[$k]=(int)($ing[$k]??0);
   $pub=app(ArticleGenerationService::class)->publishBatch();foreach(['scanned'=>'candidates_scanned','published'=>'published','rejected'=>'rejected','deferred'=>'deferred','source_fetch_success'=>'source_fetch_success','source_fetch_failed'=>'source_fetch_failed','generated_ai'=>'generated_ai','generated_fallback'=>'generated_fallback','generation_failed'=>'generation_failed','quality_pass'=>'quality_pass','quality_failed'=>'quality_failed'] as $from=>$to)$t[$to]=(int)($pub[$from]??0);
   $status='completed';
  }catch(\\Throwable $e){$status='failed';$err=substr($e->getMessage(),0,1000);}
  finally{$t['duration_ms']=(int)round((microtime(true)-$started)*1000);if($run)$run->update(['status'=>$status,'processed'=>$t['published'],'telemetry'=>$t,'last_error'=>$err,'finished_at'=>now()]);$lock->refresh();if($lock->owner===$owner)$lock->update(['owner'=>null,'expires_at'=>now()]);}
  return ['status'=>$status,'run_id'=>$owner,'telemetry'=>$t,'last_error'=>$err];
 }
 }
}