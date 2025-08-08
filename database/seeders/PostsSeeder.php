<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostsSeeder extends Seeder
{
    public function run(): void
    {
        $userId  = DB::table('users')->where('email', 'demo@example.com')->value('id');
        if (!$userId) return;

        $musicIds = DB::table('musics')->pluck('id')->all();
        $tagIds   = DB::table('tags')->pluck('id')->all();

        // 安全装置
        if (empty($musicIds) || empty($tagIds)) return;

        // 既存のデモ投稿を二重作成しない
        $hasDemo = DB::table('posts')->where('user_id', $userId)->exists();
        if ($hasDemo) return;

        foreach ($musicIds as $i => $mid) {
            $postId = DB::table('posts')->insertGetId([
                'user_id'    => $userId,
                'music_id'   => $mid,
                'audio_path' => 'audio/sample' . (($i % 3) + 1) . '.mp4', // 任意のデモ音源
                'description'=> 'デモ投稿 ' . ($i + 1) . '（ボーカル特徴での検索検証用）',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ランダムに2〜4タグを付与
            shuffle($tagIds);
            $attach = array_slice($tagIds, 0, rand(2, 4));
            foreach ($attach as $tid) {
                DB::table('vocal_tag')->insert([
                    'vocal_id'  => $postId, // posts.id
                    'tag_id'    => $tid,    // tags.id
                    'created_at'=> now(),
                    'updated_at'=> now(),
                ]);
            }
        }
    }
}