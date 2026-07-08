@extends('layouts.guest')
@section('title', '申込一覧 — ' . $event->title)
@section('content')

<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-std-28 font-bold">申込一覧</h1>
        <p class="text-text-sub mt-1">{{ $event->title }}</p>
    </div>
    <a href="{{ route('guest.event.export', $event) }}" class="btn btn-secondary">CSVダウンロード</a>
</div>

<div class="card overflow-x-auto">
    <table class="w-full text-std-16">
        <thead>
            <tr class="border-b border-border text-left text-text-sub">
                <th class="py-3 pr-4 font-bold">受付番号</th>
                <th class="py-3 pr-4 font-bold">時間枠</th>
                <th class="py-3 pr-4 font-bold">代表者</th>
                <th class="py-3 pr-4 font-bold">電話番号</th>
                <th class="py-3 pr-4 font-bold">申込日時</th>
                <th class="py-3 font-bold">ステータス</th>
            </tr>
        </thead>
        <tbody>
        @forelse($entries as $entry)
            <tr class="border-b border-border {{ $entry->status === 'cancelled' ? 'opacity-50' : '' }}">
                <td class="py-3 pr-4 font-bold">{{ $entry->entry_no }}</td>
                <td class="py-3 pr-4">{{ $entry->slot->game_date->format('m/d') }} {{ $entry->slot->name }}</td>
                <td class="py-3 pr-4">{{ $entry->rep_name }}</td>
                <td class="py-3 pr-4">{{ $entry->rep_phone }}</td>
                <td class="py-3 pr-4">{{ $entry->created_at->format('Y/m/d H:i') }}</td>
                <td class="py-3">{{ $entry->status === 'confirmed' ? '確認済み' : 'キャンセル' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="py-6 text-center text-text-sub">申込がありません</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

{{ $entries->links() }}
@endsection
