<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Services\Persistence\LegacyMigrationService;
class MigrateLegacy extends Command { protected $signature='berita:migrate-legacy {path=data/articles.json} {--dry-run}'; protected $description='Import legacy article JSON without destructive writes'; public function handle(LegacyMigrationService $service):int{$r=$service->import($this->argument('path'),(bool)$this->option('dry-run'));$this->line(json_encode($r,JSON_PRETTY_PRINT));return 0;} }
