<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectQuotesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('project_quotes', function (Blueprint $table) {
      $table->id();
      $table->string('uuid', 36);
      $table->string('description', 255)->nullable();
      $table->string('uri', 255);
      $table->foreignId('project_id')->constrained();
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
    Schema::dropIfExists('project_quotes');
  }
}
