<?php
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;
Artisan::command('berita:cycle',function(){$this->call('berita:ingest');$this->call('berita:publish');})->purpose('Run news ingestion and bounded publishing cycle');
Schedule::command('berita:cycle')->everyFifteenMinutes()->withoutOverlapping(20);
Schedule::command('berita:instagram-prepare')->everyFifteenMinutes()->withoutOverlapping(20);
Schedule::command('berita:instagram-publish')->everyFifteenMinutes()->withoutOverlapping(20);
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=2')->everyFiveMinutes()->withoutOverlapping(5);
