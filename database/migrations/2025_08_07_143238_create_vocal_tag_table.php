<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocal_tag', function (Blueprint $table) {
            $table->id();                               // bigint 主キー

            $table->foreignId('vocal_id')               // posts.id
                  ->constrained('posts')
                  ->cascadeOnDelete();

            $table->foreignId('tag_id')                 // tags.id
                  ->constrained('tags')
                  ->cascadeOnDelete();

            $table->timestamps();                       // created_at

            // 同じ投稿に同じタグを重複登録しないよう複合ユニーク
            $table->unique(['vocal_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocal_tag');
    }
};