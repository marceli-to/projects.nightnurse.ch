<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('projects', function (Blueprint $table) {
      $table->id();
      $table->string('uuid', 36);
      $table->string('number', 255);
      $table->string('name', 255);
      $table->date('date_start')->nullable();
      $table->date('date_end')->nullable();
      $table->foreignId('project_state_id')->constrained();
      $table->foreignId('user_id')->constrained();
      $table->foreignId('company_id')->constrained();
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
    Schema::dropIfExists('projects');
  }
}
