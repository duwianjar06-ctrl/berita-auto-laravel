<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('berita:cycle')->everyFifteenMinutes()->withoutOverlapping(20);
Schedule::command('berita:instagram-prepare')->everyFifteenMinutes()->withoutOverlapping(20);
Schedule::command('berita:instagram-publish')->everyFifteenMinutes()->withoutOverlapping(20);
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=2')->everyFiveMinutes()->withoutOverlapping(5);
