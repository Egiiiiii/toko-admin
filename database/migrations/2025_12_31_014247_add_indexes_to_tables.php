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
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');      // Mempercepat filter Pending/Completed
            $table->index('created_at');  // Mempercepat Grafik per Tanggal
            $table->index('customer_id'); // Mempercepat relasi customer
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('product_id');  // Mempercepat pencarian produk terlaris
            $table->index('order_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('paid_at');     // Mempercepat filter tanggal bayar
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};
