<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_kelas', function (Blueprint $table) {
            $table->id('id_jadwal_kelas');
            $table->foreignId('id_kelas')->constrained('kelas', 'id_kelas')->cascadeOnDelete();
            $table->foreignId('id_instruktur')->nullable()->constrained('instruktur', 'id_instruktur')->nullOnDelete();
            $table->timestamp('tanggal_kelas')->nullable();
            $table->timestamp('jam_mulai')->nullable();
            $table->timestamp('jam_selesai')->nullable();
            $table->integer('kuota_maksimal')->nullable();
            $table->integer('kuota_terisi')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_kelas');
    }
};
