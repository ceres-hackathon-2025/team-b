<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Music;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->get();
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Music::create([
            'title' => "test music",
            'photo_path' => "fsa"
        ]);
        Post::create([
            'user_id' => 1,
            'audio_path' => "test_audio_path",
            'music_id' => 1,
            'description' => $request->description
        ]);
        
        return redirect()->route('posts.index')->with('success', '投稿しました！');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
