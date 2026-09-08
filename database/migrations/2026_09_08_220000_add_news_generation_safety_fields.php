<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void{Schema::table('articles',function(Blueprint $t){$t->unsignedInteger('generation_attempts')->default(0)->after('generation_status');$t->text('last_generation_error')->nullable()->after('generation_attempts');$t->text('rejected_reason')->nullable()->after('last_generation_error');$t->text('deferred_reason')->nullable()->after('rejected_reason');});}
 public function down():void{Schema::table('articles',function(Blueprint $t){$t->dropColumn(['generation_attempts','last_generation_error','rejected_reason','deferred_reason']);});}
};