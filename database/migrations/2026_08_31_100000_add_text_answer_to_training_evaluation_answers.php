<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void{Schema::table('training_evaluation_answers',function(Blueprint$t){$t->text('text_answer')->nullable()->after('selected_option_ids');});}
 public function down():void{Schema::table('training_evaluation_answers',function(Blueprint$t){$t->dropColumn('text_answer');});}
};
