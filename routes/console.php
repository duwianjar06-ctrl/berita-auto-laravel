<?php
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;
use App\Models\Article;
use App\Services\Automation\NewsCycleService;
Artisan::command('berita:cycle',function(NewsCycleService $service){$r=$service->run();$this->info(json_encode($r));})->purpose('Run canonical automated news cycle');
Artisan::command('berita:publish-scheduled',function(){ $count=Article::where('generation_status','scheduled')->whereNotNull('scheduled_at')->where('scheduled_at','<=',now())->update(['generation_status'=>'published','site_published_at'=>now(),'updated_at_content'=>now(),'reviewed_at'=>now()]); $this->info('Published scheduled articles: '.$count); })->purpose('Publish reviewed articles whose schedule has arrived');
Schedule::command('berita:cycle')->everyThirtyMinutes()->withoutOverlapping(20);
Schedule::command('berita:publish-scheduled')->everyTenMinutes()->withoutOverlapping(5);
Schedule::command('berita:instagram-prepare')->everyFifteenMinutes()->withoutOverlapping(20);
Schedule::command('berita:instagram-publish')->everyFifteenMinutes()->withoutOverlapping(20);
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=2')->everyFiveMinutes()->withoutOverlapping(5);
