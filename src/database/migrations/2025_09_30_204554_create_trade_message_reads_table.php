<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trade_message_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'user_id']);        // 取引×ユーザーで一意
            $table->index(['order_id', 'last_read_at']);     // 集計用の補助index（任意）
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_message_reads');
    }
};
