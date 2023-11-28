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
    Schema::create('markups', function (Blueprint $table) {
      $table->id();
      $table->string('uuid', 36);
      $table->string('shape_uuid', 36);
      $table->text('shape');
      $table->text('comment')->nullable();
      $table->tinyInteger('is_locked')->default(0);
      $table->foreignId('message_file_id')->constrained();
      $table->foreignId('user_id')->constrained();
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
    Schema::dropIfExists('markups');
  }
};
