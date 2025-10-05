<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            // 1ユーザー = 1住所 を保証するために unique() を追加
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade')
                ->unique();

            $table->string('postal_code');        // 郵便番号
            $table->string('address');            // 住所
            $table->string('building')->nullable(); // 建物名（任意）
            $table->timestamps();                 // created_at / updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
