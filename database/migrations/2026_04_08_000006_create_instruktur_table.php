<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instruktur', function (Blueprint $table) {
            $table->id('id_instruktur');
            $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->string('spesialisasi', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instruktur');
    }
};
