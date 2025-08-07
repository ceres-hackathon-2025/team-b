<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * 検索ページ
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // ── 入力取得 ───────────────────────────────
        $q   = $request->input('q');      // キーワード
        $tag = $request->input('tag');    // タグ名（オプション）

        // ── 投稿検索 ───────────────────────────────
        $posts = Post::query()
            // キーワードで description を部分一致
            ->when($q, function ($qry) use ($q) {
                $qry->where('description', 'like', "%{$q}%");
            })
            // タグ名が指定されていればタグで絞り込み
            ->when($tag, function ($qry) use ($tag) {
                $qry->whereHas('tags', fn ($sub) => $sub->where('name', $tag));
            })
            ->latest()                   // created_at DESC
            ->paginate(12);              // 1 ページ 12 件

        // ── タグ候補（キーワードに部分一致）────────
        $tags = Tag::query()
            ->when($q, fn ($qry) => $qry->where('name', 'like', "%{$q}%"))
            ->orderByRaw('LENGTH(name)')  // 短いタグほど上位
            ->take(10)
            ->get();

        return view('search.index', compact('q', 'tag', 'posts', 'tags'));
    }
}