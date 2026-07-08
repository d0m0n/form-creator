@extends('layouts.admin')
@section('title', '参加者一覧 — ' . $event->title)
@section('content')

<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-std-28 font-bold">参加者一覧</h1>
        <p class="text-text-sub mt-1">{{ $event->title }}</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.entries.index', $event) }}" class="btn btn-secondary">申込一覧へ</a>
        <a href="{{ route('admin.export.participants', $event) }}" class="btn btn-secondary">CSVダウンロード</a>
        <a href="{{ route('admin.events.show', $event) }}" class="btn btn-secondary">← イベントへ戻る</a>
    </div>
</div>

{{-- フィルター --}}
<form method="GET" class="card mb-4">
    <div class="flex flex-wrap items-end gap-4">
        <div class="form-group">
            <label class="form-label" for="slot_id">時間枠</label>
            <select id="slot_id" name="slot_id" class="form-select w-56" onchange="this.form.submit()">
                <option value="">すべての枠</option>
                @foreach($slots as $slot)
                <option value="{{ $slot->id }}" {{ request('slot_id') == $slot->id ? 'selected' : '' }}>
                    {{ $slot->game_date->format('m/d') }} {{ $slot->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label" for="status">ステータス</label>
            <select id="status" name="status" class="form-select w-44" onchange="this.form.submit()">
                <option value="confirmed" {{ request('status', 'confirmed') === 'confirmed' ? 'selected' : '' }}>確認済みのみ</option>
                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>すべて（キャンセル含む）</option>
            </select>
        </div>
        <p class="text-std-16 text-text-sub pb-1">
            {{ $members->total() }} 名
        </p>
    </div>
</form>

<div class="card overflow-x-auto">
    <table class="w-full text-std-16 min-w-[640px]">
        <thead>
            <tr class="border-b border-border text-left text-text-sub">
                <th class="py-3 pr-4 font-bold">時間枠</th>
                <th class="py-3 pr-4 font-bold">受付番号</th>
                <th class="py-3 pr-4 font-bold">代表者</th>
                <th class="py-3 pr-4 font-bold">氏名</th>
                <th class="py-3 pr-4 font-bold">年齢</th>
                <th class="py-3 pr-4 font-bold">性別</th>
                <th class="py-3 font-bold">ステータス</th>
            </tr>
        </thead>
        <tbody>
        @php
            $genderLabels = ['male' => '男性', 'female' => '女性', 'other' => 'その他'];
        @endphp
        @forelse($members as $member)
            @php $entry = $member->entry; @endphp
            <tr class="border-b border-border {{ $entry->status === 'cancelled' ? 'opacity-50' : '' }}">
                <td class="py-3 pr-4 whitespace-nowrap">
                    {{ $entry->slot->game_date->format('m/d') }}
                    <span class="text-text-sub">{{ $entry->slot->name }}</span>
                </td>
                <td class="py-3 pr-4">
                    <a href="{{ route('admin.entries.show', [$event, $entry]) }}" class="text-link underline">
                        {{ $entry->entry_no }}
                    </a>
                </td>
                <td class="py-3 pr-4">{{ $entry->rep_name }}</td>
                <td class="py-3 pr-4 font-bold">{{ $member->name }}</td>
                <td class="py-3 pr-4">{{ $member->age }}歳</td>
                <td class="py-3 pr-4">{{ $genderLabels[$member->gender] ?? '-' }}</td>
                <td class="py-3">
                    @if($entry->status === 'confirmed')
                        <span class="text-success font-bold">確認済み</span>
                    @else
                        <span class="text-error">キャンセル</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-text-sub">参加者がいません</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{ $members->links() }}

@endsection
