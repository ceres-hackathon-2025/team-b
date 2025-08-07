@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
<h1>ログイン</h1>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<form method="POST" action="/login">
    @csrf
    <input type="email" name="email" placeholder="メールアドレス" required value="{{ old('email') }}"><br>
    <input type="password" name="password" placeholder="パスワード" required><br>
    <button type="submit">ログイン</button>
</form>

<a href="/signup">新規登録はこちら</a>

@if($errors->any())
    <div style="color: red;">
        {{ $errors->first() }}
    </div>
@endif
@endsection