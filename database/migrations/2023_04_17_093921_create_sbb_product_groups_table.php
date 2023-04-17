<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSbbProductGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sbb_product_groups', function (Blueprint $table) {
          $table->id();
          $table->string('slug', 255);
          $table->string('title', 255);
          $table->text('subtitle')->nullable();
          $table->text('teaser')->nullable();
          $table->text('description')->nullable();
          $table->text('workflow_text')->nullable();
          $table->text('workflow_file')->nullable();
          $table->string('image', 255);
          $table->text('examples')->nullable();
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
        Schema::dropIfExists('sbb_product_groups');
    }
}
