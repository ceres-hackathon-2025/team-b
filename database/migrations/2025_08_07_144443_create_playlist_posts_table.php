<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('playlist_posts', function (Blueprint $table) {
            $table->id();                                          // 主キー
            $table->foreignId('playlist_id')                       // playlists.id
                  ->constrained('playlists')
                  ->cascadeOnDelete();
            $table->foreignId('post_id')                           // posts.id
                  ->constrained('posts')
                  ->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);       // 並び順
            $table->timestamp('created_at')->useCurrent();

            // 1つのプレイリスト内で同一投稿の重複登録を防ぐ
            $table->unique(['playlist_id', 'post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('playlist_posts');
    }
};