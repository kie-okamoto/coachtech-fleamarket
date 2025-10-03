<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trade_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');                  // 本文（必須）
            $table->string('image_path')->nullable(); // 画像（任意）
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('trade_messages');
    }
};
