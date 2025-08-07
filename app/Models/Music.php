<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    use HasFactory;
    protected $table = 'musics';
    protected $fillable = ['title', 'photo_path'];
}
