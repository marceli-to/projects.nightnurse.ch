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
      // add field 'intermediate' (tinyint) in 'messages' table after body
      Schema::table('messages', function (Blueprint $table) {
        $table->tinyInteger('intermediate')->default(0)->after('body');
      });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      // drop field 'intermediate' (tinyint) in 'messages' table
      Schema::table('messages', function (Blueprint $table) {
        $table->dropColumn('intermediate');
      });
    }
};
