<?php
namespace App\Http\Controllers;
use App\Models\{Article,InstagramPost,InstagramPreparation,InstagramPublishQueue,AutomationRun,NewsSource,AutomationLock};
use App\Services\{News\NewsIngestionService,Articles\ArticleGenerationService,Instagram\PreparationService,Instagram\PublisherService};
use Illuminate\Http\Request;
class AutomationController extends Controller {
 private function cron(Request $r):bool { $secret=config('berita.cron_secret'); return !$secret || hash_equals($secret,(string)$r->header('X-Cron-Secret')); }
 public function health(){return response()->json(['database'=>\DB::connection()->getPdo()?true:false,'automation_enabled'=>(bool)config('berita.automation_enabled'),'preparation_enabled'=>(bool)config('berita.instagram_preparation_enabled'),'publisher_enabled'=>(bool)config('berita.instagram_auto_publish'),'last_preparation'=>optional(InstagramPreparation::latest()->first())->completed_at,'last_publish'=>optional(InstagramPost::where('status','PUBLISHED')->latest('published_at')->first())->published_at,'last_error'=>optional(InstagramPost::whereNotNull('last_error')->latest('updated_at')->first())->last_error]);}
 public function newsPublish(Request $r,NewsIngestionService $ingest,ArticleGenerationService $publisher){if(!$this->cron($r))return response()->json(['message'=>'Unauthorized'],401);if(!config('berita.automation_enabled'))return response()->json(['status'=>'disabled']);$a=$ingest->run();$published=0;for($i=0;$i<(int)env('NEWS_PUBLISH_TARGET',2);$i++)if($publisher->publishNext())$published++;return response()->json(['ingest'=>$a,'published'=>$published]);}
 public function socialPrepare(Request $r,PreparationService $s){if(!$this->cron($r))return response()->json(['message'=>'Unauthorized'],401);return response()->json($s->run((int)config('berita.instagram_prepare_max',3)));}
 public function socialPublish(Request $r,PublisherService $s){if(!$this->cron($r))return response()->json(['message'=>'Unauthorized'],401);return response()->json($s->run());}
 public function newsData(){return response()->json(['articles'=>Article::with('category')->latest('site_published_at')->limit(100)->get(),'sources'=>NewsSource::orderBy('name')->get()]);}
 public function instagramData(){return response()->json(['ready'=>InstagramPost::where('status','READY')->count(),'queue'=>InstagramPublishQueue::where('status','READY')->count(),'latest_preparation'=>InstagramPreparation::latest()->first(),'latest_published'=>InstagramPost::where('status','PUBLISHED')->latest('published_at')->first()]);}
}
