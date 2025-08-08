<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'ハスキー','クリア','甘い','ビブラート','ウィスパー','シャウト',
            '高音','中低音','ファルセット','力強い','息多め','繊細','伸びやか','太い'
        ];

        foreach ($tags as $name) {
            DB::table('tags')->updateOrInsert(
                ['name' => $name],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}