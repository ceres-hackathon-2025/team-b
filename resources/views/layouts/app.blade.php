<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Laravel App')</title>
</head>
<body>
    <nav>
        @auth
            <form method="POST" action="/logout">
                @csrf
                <button type="submit">ログアウト</button>
            </form>
        @endauth
    </nav>

    <main>
        @yield('content')
    </main>
</body>
</html>