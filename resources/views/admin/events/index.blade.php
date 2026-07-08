@extends('layouts.admin')
@section('title', 'イベント管理')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-std-28 font-bold">イベント管理</h1>
    <a href="{{ route('admin.events.create') }}" class="btn btn-primary">+ イベント作成</a>
</div>

<div class="card overflow-x-auto">
    <table class="w-full text-std-16">
        <thead>
            <tr class="border-b border-border text-left text-text-sub">
                <th class="py-3 pr-4 font-bold">イベント名</th>
                <th class="py-3 pr-4 font-bold">開催期間</th>
                <th class="py-3 pr-4 font-bold">ステータス</th>
                <th class="py-3 pr-4 font-bold">申込数</th>
                <th class="py-3 font-bold">操作</th>
            </tr>
        </thead>
        <tbody>
        @forelse($events as $event)
            <tr class="border-b border-border">
                <td class="py-3 pr-4 font-bold">
                    <a href="{{ route('entry.index', $event) }}" target="_blank" class="text-link underline">{{ $event->title }}</a>
                </td>
                <td class="py-3 pr-4 text-text-sub">{{ $event->start_date->format('Y/m/d') }} 〜 {{ $event->end_date->format('Y/m/d') }}</td>
                <td class="py-3 pr-4">{{ ['draft'=>'非公開','open'=>'受付中','closed'=>'受付終了'][$event->status] }}</td>
                <td class="py-3 pr-4">{{ $event->entries_count }}</td>
                <td class="py-3 flex gap-3">
                    <a href="{{ route('admin.entries.index', $event) }}" class="text-link underline">申込一覧</a>
                    <a href="{{ route('admin.events.show', $event) }}" class="text-link underline">時間枠追加</a>
                    <a href="{{ route('admin.events.edit', $event) }}" class="text-link underline">イベント編集</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="py-6 text-center text-text-sub">イベントがありません</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $events->links() }}
@endsection
