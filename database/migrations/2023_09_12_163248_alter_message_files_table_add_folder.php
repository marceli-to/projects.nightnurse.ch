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
      // add field "folder" in "message_files" table
      Schema::table('message_files', function (Blueprint $table) {
        $table->string('folder', 255)->nullable()->after('original_name');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      // drop field "folder" in "message_files" table
      Schema::table('message_files', function (Blueprint $table) {
        $table->dropColumn('folder');
      });
    }
};
