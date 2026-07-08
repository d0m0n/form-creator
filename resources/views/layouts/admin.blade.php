<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '管理画面') | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-text">

<a href="#main" class="skip-link">本文へスキップ</a>

<header class="bg-primary text-white">
    <div class="layout-admin py-3 flex items-center justify-between">
        <a href="{{ route('admin.dashboard') }}" class="text-std-18 font-bold text-white no-underline">管理画面</a>
        <div class="flex items-center gap-4 text-std-16">
            <span>{{ auth('admin')->user()->name }}</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-white underline cursor-pointer bg-transparent border-0">ログアウト</button>
            </form>
        </div>
    </div>
</header>

<div class="layout-admin mt-8 flex gap-8 items-start">

    {{-- サイドナビ --}}
    <nav class="w-48 flex-shrink-0" aria-label="管理メニュー">
        @php $current = request()->route()->getName(); @endphp
        <ul class="space-y-1 text-std-16">
            <li>
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-sm no-underline font-bold
                          {{ str_starts_with($current, 'admin.dashboard') ? 'bg-primary text-white' : 'text-text hover:bg-tertiary' }}">
                    ダッシュボード
                </a>
            </li>
            <li class="pt-3">
                <p class="px-3 text-std-14 font-bold text-text-sub mb-1">イベント</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.events.index') }}"
                           class="flex items-center gap-2 px-3 py-2 rounded-sm no-underline
                                  {{ str_starts_with($current, 'admin.events') ? 'bg-primary text-white font-bold' : 'text-text hover:bg-tertiary' }}">
                            イベント一覧
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.events.create') }}"
                           class="flex items-center gap-2 px-3 py-2 rounded-sm no-underline
                                  {{ $current === 'admin.events.create' ? 'bg-primary text-white font-bold' : 'text-text hover:bg-tertiary' }}">
                            + イベント作成
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    {{-- メインコンテンツ --}}
    <main id="main" class="flex-1 min-w-0">
        @if(session('success'))
        <div class="p-4 mb-6 bg-success-bg text-success rounded-md" role="alert">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="p-4 mb-6 bg-error-bg text-error rounded-md" role="alert">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

</div>

</body>
</html>
