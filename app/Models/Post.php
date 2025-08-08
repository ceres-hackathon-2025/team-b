<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    protected $fillable = [
        'description',
        'user_id',
        'audio_path',
        'music_id'
    ];

    /**
     * 紐づくユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 紐づく音源
     */
    public function music()
    {
        return $this->belongsTo(Music::class);
    }

    /**
     * ボーカル特徴タグ（多対多）
     * 中間テーブル: vocal_tag
     * posts.id ↔ vocal_tag.vocal_id
     * tags.id  ↔ vocal_tag.tag_id
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'vocal_tag', 'vocal_id', 'tag_id')
                    ->withTimestamps();
    }

    /**
     * いいね
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * 再生ビュー
     */
    public function views()
    {
        return $this->hasMany(View::class);
    }
}

