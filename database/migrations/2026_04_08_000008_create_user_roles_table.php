<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->foreignId('id_role')->constrained('roles', 'id_role')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->primary(['id_user', 'id_role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
