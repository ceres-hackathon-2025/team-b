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
        $musics = Music::all(); // 負荷、ここでは見逃していただきたい
        return view('posts.create', compact('musics'));
    }

    // 投稿を作成
    public function store(Request $request)
    {
        $request->validate([
            'audio' => 'required|mimes:mp3,wav,m4a',
            'description' => 'required|string|max:255',
            'music_id' => 'required|exists:musics,id',
        ]);

        if ($request->file('audio')) {
            $file = $request->file('audio');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('post-audio', $filename, 'public');
        } else {
            return redirect()->back()->with('error', '音声ファイルがありません。');
        }

        $post = Post::create([
            'user_id' => Auth::id(),
            'audio_path' => $path,
            'music_id' => $request->music_id,
            'description' => $request->description
        ]);

        return redirect()->route('posts.show', $post->id)->with('success', '投稿しました！');
    }

    // 個別の投稿を表示するが疑似SPAで事実上タイムライン
    public function show(string $id)
    {
        $post = Post::with(['user', 'music'])->findOrFail($id);
        return view('posts.show', compact('post'));
    }

    // タイムライン用のエンドポイント(json)
    public function load_more()
    {
        $post = Post::inRandomOrder()->first();

        if ($post) {
            $post->load(['user', 'music']);
            return response()->json($post);
        }

        return response()->json(null, 404);
    }

}
