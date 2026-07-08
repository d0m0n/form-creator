@extends('layouts.entry')
@section('title', $event->title . ' 管理')
@section('content')
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-std-28 font-bold">{{ $event->title }}</h1>
        <p class="text-text-sub mt-1">{{ ['draft'=>'非公開','open'=>'受付中','closed'=>'受付終了'][$event->status] }}</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('guest.event.edit', $event) }}" class="btn btn-secondary">編集</a>
        <a href="{{ route('guest.event.entries', $event) }}" class="btn btn-secondary">申込一覧</a>
        <a href="{{ route('guest.event.export', $event) }}" class="btn btn-secondary">CSV</a>
    </div>
</div>

@if($event->isOpen())
<div class="card mb-6 bg-surface">
    <p class="text-text-sub text-std-16">申込フォームURL（コピーして共有してください）</p>
    <p class="font-bold mt-1 break-all">{{ route('entry.index', $event) }}</p>
</div>
@endif

<h2 class="text-std-22 font-bold mb-4">時間枠</h2>
<div class="card overflow-x-auto">
    <table class="w-full text-std-16">
        <thead><tr class="border-b border-border text-left text-text-sub"><th class="py-3 pr-4 font-bold">開催日</th><th class="py-3 pr-4 font-bold">枠名</th><th class="py-3 pr-4 font-bold">時間</th><th class="py-3 pr-4 font-bold">定員</th><th class="py-3 font-bold">申込</th></tr></thead>
        <tbody>
        @forelse($slots as $slot)
            <tr class="border-b border-border">
                <td class="py-3 pr-4">{{ $slot->game_date->format('Y/m/d') }}</td>
                <td class="py-3 pr-4">{{ $slot->name }}</td>
                <td class="py-3 pr-4">{{ $slot->start_time }}〜{{ $slot->end_time }}</td>
                <td class="py-3 pr-4">{{ $slot->capacity }}名</td>
                <td class="py-3">{{ $slot->confirmed_count }}名</td>
            </tr>
        @empty
            <tr><td colspan="5" class="py-6 text-center text-text-sub">時間枠がありません</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
