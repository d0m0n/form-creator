@extends('layouts.admin')
@section('title', 'ダッシュボード')
@section('content')
<h1 class="text-std-28 font-bold mb-8">ダッシュボード</h1>

<div class="flex justify-between items-center mb-4">
    <h2 class="text-std-22 font-bold">イベント一覧</h2>
    <a href="{{ route('admin.events.create') }}" class="btn btn-primary">+ イベント作成</a>
</div>

<div class="card overflow-x-auto">
    <table class="w-full text-std-16" aria-label="イベント一覧">
        <thead>
            <tr class="border-b border-border text-left text-text-sub">
                <th class="py-3 pr-4 font-bold">イベント名</th>
                <th class="py-3 pr-4 font-bold">ステータス</th>
                <th class="py-3 pr-4 font-bold">申込数</th>
                <th class="py-3 font-bold">操作</th>
            </tr>
        </thead>
        <tbody>
        @forelse($events as $event)
            <tr class="border-b border-border">
                <td class="py-3 pr-4">{{ $event->title }}</td>
                <td class="py-3 pr-4">{{ ['draft'=>'非公開','open'=>'受付中','closed'=>'受付終了'][$event->status] }}</td>
                <td class="py-3 pr-4">{{ $event->entries_count }}</td>
                <td class="py-3"><a href="{{ route('admin.events.show', $event) }}" class="text-link underline">詳細</a></td>
            </tr>
        @empty
            <tr><td colspan="4" class="py-6 text-center text-text-sub">イベントがありません</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
