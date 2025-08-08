<?php

// app/Models/Tag.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'vocal_tag', 'tag_id', 'vocal_id')
                    ->withTimestamps();
    }
}