<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trade_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete(); // 紐づく取引
            $table->foreignId('rater_id')->constrained('users')->cascadeOnDelete(); // 評価した人
            $table->foreignId('rated_user_id')->constrained('users')->cascadeOnDelete(); // 評価された人
            $table->unsignedTinyInteger('score'); // 1〜5
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'rater_id']); // 同じ取引での二重評価防止
            $table->index(['rated_user_id', 'score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_reviews');
    }
};
