<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('messages', function (Blueprint $table) {
        $table->json('comments')->nullable()->after('body');
        $table->tinyInteger('is_comment')->default(0)->after('comments');
        $table->foreignId('commentable_file_id')->nullable()->after('is_comment')->constrained('message_files')->onDelete('cascade');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('messages', function (Blueprint $table) {
        $table->dropColumn('comments');
      });
    }
};
