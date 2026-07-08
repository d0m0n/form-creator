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
                <th class="py-3 pr-4 font-bold">申込期限</th>
                <th class="py-3 pr-4 font-bold">受付人数</th>
                <th class="py-3 pr-4 font-bold">申込数</th>
                <th class="py-3 font-bold">申込人数</th>
            </tr>
        </thead>
        <tbody>
        @forelse($events as $event)
            @php $capacity = (int) $event->total_capacity; @endphp
            <tr class="border-b border-border">
                <td class="py-3 pr-4 font-bold">
                    <a href="{{ route('entry.index', $event) }}" target="_blank" class="text-link underline">{{ $event->title }}</a>
                </td>
                <td class="py-3 pr-4">{{ ['draft'=>'非公開','open'=>'受付中','closed'=>'受付終了'][$event->status] }}</td>
                <td class="py-3 pr-4">
                    @if($event->entry_deadline)
                        {{ $event->entry_deadline->format('Y/m/d') }}
                        @if($event->isDeadlinePassed())
                            <span class="text-error text-std-14 font-bold ml-1">（期限切れ）</span>
                        @endif
                    @else
                        <span class="text-text-sub">—</span>
                    @endif
                </td>
                <td class="py-3 pr-4">{{ $capacity > 0 ? $capacity.'名' : '—' }}</td>
                <td class="py-3 pr-4">{{ $event->entries_count }}件</td>
                <td class="py-3">
                    {{ $event->members_count }}名
                    @if($capacity > 0)
                        <span class="text-text-sub text-std-14 ml-1">/ {{ $capacity }}名</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="py-6 text-center text-text-sub">イベントがありません</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
