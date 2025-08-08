<?php

// app/Models/Music.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    protected $table = 'musics';
    protected $fillable = ['title','photo_path'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
