@extends('layouts/app')

@section('title', '投稿一覧')

@section('content')
    <h1>投稿一覧</h1>
    
    @if (session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif
    
    <a href="{{ route('posts.create') }}" class="btn">新しい投稿を作成</a>
    
    @if ($posts->count() > 0)
        @foreach ($posts as $post)
            <div class="post">
                <div class="post-description">{{ $post->description }}</div>
                <div class="post-date">作成日: {{ $post->created_at->format('Y年m月d日 H:i') }}</div>
            </div>
        @endforeach
    @else
        <p>まだ投稿がありません。最初の投稿を作成してみましょう！</p>
    @endif
@endsection