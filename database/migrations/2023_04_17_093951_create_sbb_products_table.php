<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSbbProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sbb_products', function (Blueprint $table) {
          $table->id();
          $table->string('size', 255)->nullable();
          $table->text('size_description')->nullable();
          $table->text('description')->nullable();
          $table->text('additional_info')->nullable();
          $table->float('price', 8, 2)->default('0.00');
          $table->foreignId('sbb_product_group_id')->constrained();
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
        Schema::dropIfExists('sbb_products');
    }
}
