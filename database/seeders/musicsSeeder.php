<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class musicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('musics')->insert([
            'title' => "Soranji",
            'photo_path' => "storage/app/public/musics/soranji.jpg",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('musics')->insert([
            'title' => "怪獣の花歌",
            'photo_path' => "storage/app/public/musics/kaijuu_no_hanauta.jpg",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('musics')->insert([
            'title' => "僕らまた",
            'photo_path' => "storage/app/public/musics/bokura_mata.jpg",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
