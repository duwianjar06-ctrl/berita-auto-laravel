<?php

namespace App\Console\Commands;

use App\Services\Persistence\LegacyMigrationService;
use Illuminate\Console\Command;

class MigrateLegacy extends Command
{
    protected $signature = 'berita-auto:import {path : Path to legacy export JSON} {--dry-run : Validate and report without writing}';

    protected $description = 'Import legacy Berita Auto article JSON idempotently';

    public function handle(LegacyMigrationService $service): int
    {
        try {
            $report = $service->import($this->argument('path'), (bool) $this->option('dry-run'));
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return ($report['failed'] ?? 0) > 0 ? self::FAILURE : self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
