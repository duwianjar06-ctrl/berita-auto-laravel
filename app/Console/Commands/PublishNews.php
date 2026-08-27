<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Services\Articles\ArticleGenerationService;
class PublishNews extends Command { protected $signature='berita:publish'; protected $description='Publish at most the configured number of eligible articles'; public function handle(ArticleGenerationService $service):int{if(!config('berita.automation_enabled')){$this->warn('AUTOMATION_ENABLED=false');return 0;} $max=(int)env('NEWS_PUBLISH_TARGET',2);$count=0;for($i=0;$i<$max;$i++){if(!$service->publishNext())break;$count++;}$this->info("published={$count}");return 0;} }
