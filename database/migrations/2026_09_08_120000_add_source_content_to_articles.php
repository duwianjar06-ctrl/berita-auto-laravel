<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{Schema::table('articles',function(Blueprint $t){$t->longText('source_content')->nullable()->after('content');$t->string('source_content_method')->nullable()->after('source_content');$t->unsignedInteger('source_content_chars')->nullable()->after('source_content_method');$t->string('source_fetch_status')->nullable()->after('source_content_chars');$t->timestamp('source_fetched_at')->nullable()->after('source_fetch_status');});}public function down():void{Schema::table('articles',function(Blueprint $t){$t->dropColumn(['source_content','source_content_method','source_content_chars','source_fetch_status','source_fetched_at']);});}};
