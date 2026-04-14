<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian_package', function (Blueprint $table) {
            $table->id('id_pembelian_package');
            $table->foreignId('id_pelanggan')->constrained('pelanggan', 'id_pelanggan')->cascadeOnDelete();
            $table->foreignId('id_package')->constrained('package', 'id_package');
            $table->foreignId('id_promo')->nullable()->constrained('promo', 'id_promo')->nullOnDelete();
            $table->decimal('harga_awal', 12, 2)->nullable();
            $table->decimal('diskon', 12, 2)->nullable();
            $table->decimal('harga_akhir', 12, 2)->nullable();
            $table->string('status_pembelian', 20)->nullable();
            $table->integer('kredit_earned')->nullable();
            $table->integer('sisa_kredit')->nullable();
            $table->timestamp('tanggal_pembelian')->useCurrent();
            $table->timestamp('tanggal_kadaluarsa')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian_package');
    }
};
