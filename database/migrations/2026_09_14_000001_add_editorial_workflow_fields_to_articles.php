<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedTinyInteger('quality_score')->nullable()->after('generation_attempts');
            $table->text('quality_reasons')->nullable()->after('quality_score');
            $table->timestamp('scheduled_at')->nullable()->after('site_published_at');
            $table->timestamp('reviewed_at')->nullable()->after('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['quality_score', 'quality_reasons', 'scheduled_at', 'reviewed_at']);
        });
    }
};
