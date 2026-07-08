<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'イベント申込') | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-text">

<a href="#main" class="skip-link">本文へスキップ</a>

<header class="border-b border-border bg-surface">
    <div class="layout-entry py-4">
        <p class="text-std-22 font-bold">{{ $event->title ?? config('app.name') }}</p>
    </div>
</header>

@isset($event)
@if($event->header_image)
<div class="w-full bg-surface">
    <img src="{{ Storage::url($event->header_image) }}"
         alt="{{ $event->title }}"
         class="w-full object-cover"
         style="max-height:320px;">
</div>
@endif
@endisset

@isset($step)
<div class="layout-entry pt-6 pb-0">
    <nav class="stepper" aria-label="申込の手順">
        <ol class="stepper__list">
            @foreach(['入力', '確認', '完了'] as $i => $label)
            <li class="stepper__item {{ $step === $i+1 ? 'is-current' : ($step > $i+1 ? 'is-done' : '') }}"
                aria-current="{{ $step === $i+1 ? 'step' : 'false' }}">
                <span class="stepper__num">{{ $i+1 }}</span>
                <span class="stepper__label">{{ $label }}</span>
            </li>
            @endforeach
        </ol>
    </nav>
</div>
@endisset

<main id="main" class="layout-entry">
    @if(session('success'))
    <div class="p-4 mb-6 bg-success-bg text-success rounded-md" role="alert">{{ session('success') }}</div>
    @endif

    @yield('content')
</main>

<footer class="border-t border-border mt-12">
    <div class="layout-entry py-6 text-text-sub text-std-16">
        @isset($event)
        @if($event->contact_email)
        <p>お問合せ: <a href="mailto:{{ $event->contact_email }}">{{ $event->contact_email }}</a></p>
        @endif
        @endisset
    </div>
</footer>

</body>
</html>
