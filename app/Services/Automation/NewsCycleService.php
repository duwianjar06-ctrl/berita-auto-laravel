<?php
namespace App\Services\Automation;
use App\Models\{AutomationRun,AutomationLock};
use App\Services\News\NewsIngestionService;
use App\Services\Articles\ArticleGenerationService;
use Illuminate\Support\Str;
class NewsCycleService {
 public function run():array{
  if(!config('berita.automation_enabled'))return ['status'=>'disabled'];
  $started=microtime(true);$run=AutomationRun::create(['run_uuid'=>(string)Str::uuid(),'type'=>'news_cycle','trigger'=>'scheduler','status'=>'running','target'=>(int)config('berita.news_publish_target',2),'started_at'=>now(),'telemetry'=>[]]);
  $lock=AutomationLock::firstOrCreate(['name'=>'news_cycle']);if($lock->expires_at&&$lock->expires_at->isFuture())return ['status'=>'locked'];
  $lock->update(['owner'=>$run->run_uuid,'expires_at'=>now()->addMinutes(10)]);
  $t=array_fill_keys(['sources_checked','sources_failed','rss_items_seen','new_candidates','duplicates_skipped','candidates_scanned','source_fetch_success','source_fetch_failed','generated_ai','generated_fallback','generation_failed','quality_pass','quality_failed','published','rejected','deferred'],0);$err=null;
  try{$ing=app(NewsIngestionService::class)->run();foreach(['sources_checked','sources_failed','rss_items_seen','new_candidates','duplicates_skipped'] as $k)$t[$k]=(int)($ing[$k]??0);$pub=app(ArticleGenerationService::class)->publishBatch();$t['candidates_scanned']=$pub['scanned'];$t['published']=$pub['published'];$t['rejected']=$pub['rejected'];$t['deferred']=$pub['deferred'];$t['generation_failed']=$pub['failed'];$t['quality_pass']=$pub['published'];$t['quality_failed']=$pub['rejected'];$status='completed';}catch(\Throwable $e){$status='failed';$err=substr($e->getMessage(),0,1000);}
  $t['duration_ms']=(int)round((microtime(true)-$started)*1000);$run->update(['status'=>$status,'processed'=>$t['published'],'telemetry'=>$t,'last_error'=>$err,'finished_at'=>now()]);$lock->update(['owner'=>null,'expires_at'=>now()]);return ['status'=>$status,'run_id'=>$run->run_uuid,'telemetry'=>$t,'last_error'=>$err];
 }
}