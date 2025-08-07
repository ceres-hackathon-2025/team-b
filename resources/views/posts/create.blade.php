@extends('layouts/app')

@section('title', 'アップロード')

@section('content')
    <form action="{{ route('posts.store') }}" method="POST">
        @csrf
        
        <div class="">
            <label for="description">内容:</label>
            <textarea name="description" id="description" rows="5" required>{{ old('description') }}</textarea>
        </div>
        
        <button type="submit">メモを保存</button>
        <a href="">一覧に戻る</a>
    </form>
@endsection
