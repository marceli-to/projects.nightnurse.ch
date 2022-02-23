<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_files', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36);
            $table->string('name', 255);
            $table->string('original_name', 255);
            $table->string('extension', 4);
            $table->float('size', 24, 0)->default(0);
            $table->tinyInteger('preview')->default(0);
            $table->foreignId('message_id')->nullable()->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_files');
    }
}
