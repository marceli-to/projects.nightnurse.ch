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
      Schema::create('feedback', function (Blueprint $table) {
        $table->id();
        $table->string('uuid', 36);
        $table->integer('rating')->default(0);
        $table->text('comment')->nullable();
        $table->foreignId('project_id')->constrained();
        $table->foreignId('user_id')->constrained();
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
      Schema::dropIfExists('feedback');
    }
};
