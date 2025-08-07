<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ── 追加カラム ─────────────────────────────
            // アバター画像への相対パスまたは URL
            $table->string('avatar_url')->nullable()->after('email');

            // 自己紹介（Slack の “About” 的フィールド）
            $table->text('bio')->nullable()->after('avatar_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // down() では必ず dropColumn で巻き戻せるように
            $table->dropColumn(['avatar_url', 'bio']);
        });
    }
};