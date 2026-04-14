<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package', function (Blueprint $table) {
            $table->id('id_package');
            $table->string('nama_package', 100)->nullable();
            $table->integer('jumlah_kredit')->nullable();
            $table->decimal('harga', 12, 2)->nullable();
            $table->integer('masa_berlaku')->nullable();
            $table->string('status_package', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package');
    }
};
