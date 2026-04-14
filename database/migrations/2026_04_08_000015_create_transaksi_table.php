<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->string('order_id', 100)->unique();
            $table->foreignId('id_pembelian_package')->constrained('pembelian_package', 'id_pembelian_package')->cascadeOnDelete();
            $table->decimal('jumlah_bayar', 12, 2)->nullable();
            $table->string('midtrans_order_id', 100)->nullable();
            $table->text('snap_token')->nullable();
            $table->string('transaction_status', 50)->nullable();
            $table->string('fraud_status', 50)->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->json('payment_response')->nullable();
            $table->string('status_internal', 50)->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('order_id', 'idx_transaksi_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
