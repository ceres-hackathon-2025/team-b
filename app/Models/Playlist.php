<?php

// Playlist.php
public function posts()
{
    return $this->belongsToMany(Post::class, 'playlist_posts')
                ->withPivot('position')
                ->orderBy('playlist_posts.position');
}