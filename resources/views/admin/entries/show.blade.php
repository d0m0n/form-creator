@extends('layouts.admin')
@section('title', '申込詳細')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-std-28 font-bold">申込詳細</h1>
    <a href="{{ route('admin.entries.index', $event) }}" class="btn btn-secondary">← 一覧へ戻る</a>
</div>

<div class="card max-w-[760px] space-y-4 mb-6">
    <dl class="grid grid-cols-[160px_1fr] gap-y-3 text-std-16">
        <dt class="font-bold text-text-sub">受付番号</dt><dd>{{ $entry->entry_no }}</dd>
        <dt class="font-bold text-text-sub">時間枠</dt><dd>{{ $entry->slot->game_date->format('Y/m/d') }} {{ $entry->slot->start_time }}〜{{ $entry->slot->end_time }}</dd>
        <dt class="font-bold text-text-sub">代表者氏名</dt><dd>{{ $entry->rep_name }}</dd>
        <dt class="font-bold text-text-sub">電話番号</dt><dd>{{ $entry->rep_phone }}</dd>
        <dt class="font-bold text-text-sub">メール</dt><dd>{{ $entry->email }}</dd>
        <dt class="font-bold text-text-sub">申込日時</dt><dd>{{ $entry->created_at->format('Y/m/d H:i') }}</dd>
        <dt class="font-bold text-text-sub">ステータス</dt>
        <dd>
            <form method="POST" action="{{ route('admin.entries.updateStatus', [$event, $entry]) }}" class="flex items-center gap-3">
                @csrf @method('PATCH')
                <select name="status" class="form-select w-36">
                    <option value="confirmed" {{ $entry->status === 'confirmed' ? 'selected' : '' }}>確認済み</option>
                    <option value="cancelled" {{ $entry->status === 'cancelled' ? 'selected' : '' }}>キャンセル</option>
                </select>
                <button type="submit" class="btn btn-secondary" style="min-width:auto;padding:8px 16px;">更新</button>
            </form>
        </dd>
    </dl>
</div>

<div class="card max-w-[760px]">
    <h2 class="text-std-18 font-bold mb-4">参加者一覧</h2>
    <table class="w-full text-std-16">
        <thead><tr class="border-b border-border text-left text-text-sub"><th class="py-2 pr-4 font-bold">No.</th><th class="py-2 pr-4 font-bold">氏名</th><th class="py-2 pr-4 font-bold">年齢</th><th class="py-2 font-bold">性別</th></tr></thead>
        <tbody>
        @foreach($entry->members as $member)
            <tr class="border-b border-border">
                <td class="py-2 pr-4">{{ $member->sort_order }}</td>
                <td class="py-2 pr-4">{{ $member->name }}</td>
                <td class="py-2 pr-4">{{ $member->age }}歳</td>
                <td class="py-2">{{ $member->genderLabel() }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
