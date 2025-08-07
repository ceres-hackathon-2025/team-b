<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Music;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // 廃止
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->get();
        return view('posts.index', compact('posts'));
    }

    // 投稿の作成画面を表示
    public function create()
    {
        return view('posts.create');
    }

    // 投稿を作成
    public function store(Request $request)
    {
        $request->validate([
            'audio' => 'required|mimes:mp3,wav,m4a',
            'description' => 'required|string|max:255',
        ]);

        if ($request->file('audio')) {
            $file = $request->file('audio');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('post-audio', $filename, 'public');
        } else {
            return redirect()->back()->with('error', '音声ファイルがありません。');
        }

        # 仮
        $music = Music::create([
            'title' => "test music",
            'photo_path' => "fsa"
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'audio_path' => $path,
            'music_id' => $music->id,
            'description' => $request->description
        ]);

        return redirect()->route('posts.show', $post->id)->with('success', '投稿しました！');
    }

    // 個別の投稿を表示するが疑似SPAで事実上タイムライン
    public function show(string $id)
    {
        $post = Post::findOrFail($id);
        return view('posts.show', compact('post'));
    }

    // タイムライン用のエンドポイント(json)
    public function load_more()
    {
        // return json
    }

}
