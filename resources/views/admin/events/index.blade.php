@extends('layouts.admin')
@section('title', 'イベント管理')
@section('content')
<div class="flex flex-wrap justify-between items-center gap-4 mb-6">
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
                <td class="py-3">
                    <div class="flex flex-col gap-1 text-std-16">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.entries.index', $event) }}" class="text-link underline whitespace-nowrap">申込一覧</a>
                            <span class="text-border select-none">·</span>
                            <a href="{{ route('admin.entries.participants', $event) }}" class="text-link underline whitespace-nowrap">参加者一覧</a>
                        </div>
                        <div class="flex items-center gap-2 text-text-sub">
                            <a href="{{ route('admin.events.show', $event) }}" class="text-link underline whitespace-nowrap">時間枠追加</a>
                            <span class="text-border select-none">·</span>
                            <a href="{{ route('admin.events.edit', $event) }}" class="text-link underline whitespace-nowrap">イベント編集</a>
                        </div>
                    </div>
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
