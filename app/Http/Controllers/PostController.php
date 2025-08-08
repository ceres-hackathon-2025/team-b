<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Music;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    // ランダムに/posts/{id}へリダイレクト
    public function index()
    {
        $post = Post::inRandomOrder()->first();
        if (!$post) {
            return redirect()->route('home')->with('error', '投稿がありません。');
        }
        return redirect()->route('posts.show', $post->id);
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
        ], [
            'audio.required' => '音声ファイルを選択してください。',
            'audio.file' => 'アップロードされたものはファイルではありません。',
            'audio.mimes' => '対応している音声形式は mp3, wav, aac のみです。',
            'audio.uploaded' => '音声ファイルについて、原因不明のエラー。',
            'description.required' => '説明文を入力してください。',
            'description.max' => '説明文は255文字以内で入力してください。',
            'music_id.required' => '音楽を選択してください。',
            'music_id.exists' => '選択した音楽は存在しません。',
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

    /**
     * @return JsonResponse
     */
    // タイムライン用のエンドポイント(json)
    public function load_more(): JsonResponse
    {
        $post = Post::inRandomOrder()->first();

        if ($post) {
            $post->load(['user', 'music']);
            return response()->json($post);
        }

        return response()->json(null, 404);
    }

}
