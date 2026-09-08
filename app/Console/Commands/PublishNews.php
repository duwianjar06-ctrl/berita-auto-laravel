<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;use App\Services\Articles\ArticleGenerationService;
class PublishNews extends Command {protected $signature='berita:publish';protected $description='Publish eligible news candidates';public function handle(ArticleGenerationService $service):int{if(!config('berita.automation_enabled')){$this->warn('AUTOMATION_ENABLED=false');return 0;}$r=$service->publishBatch();$this->info(json_encode($r));return 0;}}
