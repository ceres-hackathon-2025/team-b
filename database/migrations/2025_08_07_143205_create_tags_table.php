<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();                 // bigint 主キー
            $table->string('name')->unique();
            $table->timestamps();         // created_at / updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};