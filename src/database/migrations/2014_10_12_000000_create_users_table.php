<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                          // 主キー
            $table->string('name');                // ユーザー名
            $table->string('email')->unique();     // メールアドレス（ユニーク）
            $table->timestamp('email_verified_at')->nullable(); // メール認証日時
            $table->string('password');            // パスワード
            $table->string('profile_image')->nullable(); // プロフィール画像パス
            $table->rememberToken();               // ログイン状態保持
            $table->timestamps();                  // 作成日・更新日
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
