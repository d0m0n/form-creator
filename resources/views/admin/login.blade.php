<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-background flex items-center justify-center min-h-screen">
<main class="w-full max-w-[400px] px-4">
    <h1 class="text-std-28 font-bold mb-8 text-center">管理者ログイン</h1>

    @if($errors->any())
    <div class="error-summary mb-6" role="alert">
        <ul class="error-summary__list">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}" class="card space-y-5">
        @csrf
        <div class="form-group">
            <label class="form-label" for="email">メールアドレス</label>
            <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" autocomplete="email" required aria-required="true">
        </div>
        <div class="form-group">
            <label class="form-label" for="password">パスワード</label>
            <input type="password" id="password" name="password" class="form-input" autocomplete="current-password" required aria-required="true">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" id="remember" name="remember" class="w-4 h-4">
            <label for="remember" class="text-std-16">ログイン状態を保持する</label>
        </div>
        <button type="submit" class="btn btn-primary w-full">ログイン</button>
    </form>
</main>
</body>
</html>
