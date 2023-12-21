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
      $table->string('type', 50);
      $table->text('shape');
      $table->text('comment')->nullable();
      $table->boolean('is_locked')->default(FALSE);
      $table->foreignId('message_id')->constrained()->onDelete('cascade');
      $table->foreignId('message_file_id')->constrained()->onDelete('cascade');
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
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
