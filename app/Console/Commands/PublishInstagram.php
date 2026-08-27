<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Services\Instagram\PublisherService;
class PublishInstagram extends Command { protected $signature='berita:instagram-publish'; protected $description='Publish one READY Instagram item when explicitly enabled'; public function handle(PublisherService $service):int{$r=$service->run();$this->line(json_encode($r));return 0;} }
