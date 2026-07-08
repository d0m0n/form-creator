<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'イベント管理') | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-text">

<a href="#main" class="skip-link">本文へスキップ</a>

<header class="bg-primary text-white">
    <div class="layout-admin">
        {{-- 第1行: サービス名 + 新規作成リンク --}}
        <div class="flex items-center justify-between py-3 gap-3">
            <a href="{{ route('guest.event.create') }}" class="text-std-18 font-bold text-white no-underline flex-shrink-0">
                {{ config('app.name') }}
            </a>
            <a href="{{ route('guest.event.create') }}"
               class="flex-shrink-0 text-std-16 text-white no-underline hover:underline whitespace-nowrap">
                + 新しいイベントを作成
            </a>
        </div>

        {{-- 第2行: イベントナビ（イベントがある場合のみ） --}}
        @isset($event)
        <nav class="border-t border-white/20 overflow-x-auto" aria-label="イベント管理メニュー">
            <ul class="flex items-center text-std-16 whitespace-nowrap py-1">
                <li>
                    <a href="{{ route('guest.event.show', $event) }}"
                       class="inline-block px-3 py-2 rounded-sm no-underline text-white
                              {{ request()->routeIs('guest.event.show') ? 'bg-white/20 font-bold' : 'hover:bg-white/10' }}">
                        管理トップ
                    </a>
                </li>
                <li>
                    <a href="{{ route('guest.event.entries', $event) }}"
                       class="inline-block px-3 py-2 rounded-sm no-underline text-white
                              {{ request()->routeIs('guest.event.entries') ? 'bg-white/20 font-bold' : 'hover:bg-white/10' }}">
                        申込一覧
                    </a>
                </li>
                <li>
                    <a href="{{ route('guest.event.edit', $event) }}"
                       class="inline-block px-3 py-2 rounded-sm no-underline text-white
                              {{ request()->routeIs('guest.event.edit') ? 'bg-white/20 font-bold' : 'hover:bg-white/10' }}">
                        イベント編集
                    </a>
                </li>
                <li>
                    <a href="{{ route('guest.event.export', $event) }}"
                       class="inline-block px-3 py-2 rounded-sm no-underline text-white hover:bg-white/10">
                        CSVダウンロード
                    </a>
                </li>
            </ul>
        </nav>
        @endisset
    </div>
</header>

<main id="main" class="layout-admin py-6 md:py-8">
    @if(session('success'))
    <div class="p-4 mb-6 bg-success-bg text-success rounded-md" role="alert">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="p-4 mb-6 bg-error-bg text-error rounded-md" role="alert">{{ session('error') }}</div>
    @endif

    @yield('content')
</main>

</body>
</html>
