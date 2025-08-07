<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchApiController extends Controller
{
    public function index(Request $r)
    {
        $q = $r->input('q');
        $posts = \App\Models\Post::query()
            ->where('description','like',"%{$q}%")
            ->orWhereHas('tags', fn($q2)=>$q2->where('name','like',"%{$q}%"))
            ->take(5)->get(['id','description','thumb_path']);
        if (!$q) return [];

        // タイトル or タグ名を曖昧検索し、スコア順に取得
        $posts = \App\Models\Post::where('description','like',"%{$q}%")
                ->take(5)->get(['id','description','thumb_path']);

        return response()->json($posts);
    }
}
