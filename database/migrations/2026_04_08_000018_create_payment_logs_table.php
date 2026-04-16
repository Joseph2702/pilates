<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 100)->nullable()->index();
            $table->jsonb('raw_response')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

/*************  ✨ Windsurf Command ⭐  *************/
/*******  579ddb9a-e1ec-45ab-a78b-c67e1044b42b  *******/
    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
