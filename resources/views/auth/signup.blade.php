@extends('layouts.app')

@section('title', '新規登録')

@section('content')
<h1>新規登録</h1>

<form method="POST" action="/signup">
    @csrf
    <input type="text" name="name" placeholder="名前" required value="{{ old('name') }}"><br>
    <input type="email" name="email" placeholder="メールアドレス" required value="{{ old('email') }}"><br>
    <input type="password" name="password" placeholder="パスワード" required><br>
    <input type="password" name="password_confirmation" placeholder="パスワード（確認）" required><br>
    <button type="submit">登録</button>
</form>

<a href="/login">ログインはこちら</a>

@if($errors->any())
    <div style="color: red;">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
@endsection