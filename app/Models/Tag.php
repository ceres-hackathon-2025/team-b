<?php

// Tag.php
public function posts()
{
    return $this->belongsToMany(Post::class, 'post_tag');
}