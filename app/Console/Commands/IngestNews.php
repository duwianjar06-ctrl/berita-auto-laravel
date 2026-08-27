<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Services\News\NewsIngestionService;
class IngestNews extends Command { protected $signature='berita:ingest'; protected $description='Ingest official RSS/Atom sources into the article intake store'; public function handle(NewsIngestionService $service):int{$r=$service->run();$this->info(json_encode($r));return 0;} }
