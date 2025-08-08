<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('playlists', function (Blueprint $table) {
            $table->id();                                          // 主キー
            $table->foreignId('user_id')                           // 所有ユーザー
                  ->constrained()                                  // users.id
                  ->cascadeOnDelete();
            $table->string('title');                               // タイトル
            $table->text('description')->nullable();               // 説明
            $table->boolean('is_public')->default(true);           // 公開 / 非公開
            $table->timestamps();                                  // created_at / updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('playlists');
    }
};