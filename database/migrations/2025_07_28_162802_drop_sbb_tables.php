<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::dropIfExists('sbb_orders');
      Schema::dropIfExists('sbb_products');
      Schema::dropIfExists('sbb_product_groups');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
