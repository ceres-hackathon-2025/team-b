<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        // 既にあればスキップ
        $exists = DB::table('users')->where('email', 'demo@example.com')->exists();
        if ($exists) return;

        DB::table('users')->insert([
            'name'       => 'demo',
            'email'      => 'demo@example.com',
            'password'   => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
