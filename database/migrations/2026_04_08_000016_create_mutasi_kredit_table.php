<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_kredit', function (Blueprint $table) {
            $table->id('id_mutasi');
            $table->foreignId('id_pelanggan')->constrained('pelanggan', 'id_pelanggan')->cascadeOnDelete();
            $table->string('jenis_mutasi', 20)->nullable();
            $table->integer('jumlah_kredit')->nullable();
            $table->string('sumber_mutasi', 50)->nullable();
            $table->unsignedBigInteger('id_referensi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('tanggal_mutasi')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_kredit');
    }
};
