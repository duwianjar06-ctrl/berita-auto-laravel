<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('berita:ingest')->everyFifteenMinutes()->withoutOverlapping();
Schedule::command('berita:instagram-prepare')->everyFifteenMinutes()->withoutOverlapping();
Schedule::command('berita:instagram-publish')->everyFifteenMinutes()->withoutOverlapping();
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=2')->everyFiveMinutes()->withoutOverlapping();
