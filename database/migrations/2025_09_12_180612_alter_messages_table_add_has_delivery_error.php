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
      Schema::table('messages', function (Blueprint $table) {
        $table->boolean('has_delivery_error')->default(false)->after('internal');
        $table->json('delivery_errors')->nullable()->after('has_delivery_error');
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::table('messages', function (Blueprint $table) {
        $table->dropColumn('has_delivery_error');
        $table->dropColumn('delivery_errors');
      });
    }
};
