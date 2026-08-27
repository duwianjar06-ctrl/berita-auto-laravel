<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Services\Instagram\PreparationService;
class PrepareInstagram extends Command { protected $signature='berita:instagram-prepare'; protected $description='Prepare bounded Instagram READY items; never publish'; public function handle(PreparationService $service):int{$r=$service->run((int)config('berita.instagram_prepare_max',3));$this->line(json_encode($r));return 0;} }
