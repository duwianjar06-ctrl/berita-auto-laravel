<?php
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;
use App\Services\Automation\NewsCycleService;
Artisan::command('berita:cycle',function(NewsCycleService $service){$r=$service->run();$this->info(json_encode($r));})->purpose('Run canonical automated news cycle');
Schedule::command('berita:cycle')->everyFifteenMinutes()->withoutOverlapping(20);
Schedule::command('berita:instagram-prepare')->everyFifteenMinutes()->withoutOverlapping(20);
Schedule::command('berita:instagram-publish')->everyFifteenMinutes()->withoutOverlapping(20);
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=2')->everyFiveMinutes()->withoutOverlapping(5);
