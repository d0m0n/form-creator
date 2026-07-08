@extends('layouts.entry')
@section('title', '申込確認')
@php $step = 2; @endphp
@section('content')
<h1 class="text-std-28 font-bold mb-6">申込内容の確認</h1>
<p class="mb-6 text-std-16">以下の内容で申し込みます。よろしければ「申込を確定する」を押してください。</p>

<div class="card mb-6 space-y-3 text-std-16">
    <dl class="grid grid-cols-[140px_1fr] gap-y-3">
        <dt class="font-bold text-text-sub">時間枠</dt>
        <dd>{{ $slot->game_date->format('Y/m/d') }} {{ $slot->start_time }}〜{{ $slot->end_time }}</dd>
        <dt class="font-bold text-text-sub">代表者氏名</dt><dd>{{ $data['rep_name'] }}</dd>
        <dt class="font-bold text-text-sub">代表者年齢</dt><dd>{{ $data['rep_age'] }}歳</dd>
        <dt class="font-bold text-text-sub">メール</dt><dd>{{ $data['email'] }}</dd>
    </dl>
</div>

<div class="card mb-8">
    <h2 class="text-std-18 font-bold mb-4">メンバー</h2>
    @php $genderLabels = ['male'=>'男性','female'=>'女性','other'=>'その他']; @endphp
    <table class="w-full text-std-16">
        <thead><tr class="border-b border-border text-left text-text-sub"><th class="py-2 pr-4 font-bold">No.</th><th class="py-2 pr-4 font-bold">氏名</th><th class="py-2 pr-4 font-bold">年齢</th><th class="py-2 font-bold">性別</th></tr></thead>
        <tbody>
        @foreach($data['members'] as $i => $member)
            <tr class="border-b border-border">
                <td class="py-2 pr-4">{{ $i+1 }}</td>
                <td class="py-2 pr-4">{{ $member['name'] }}</td>
                <td class="py-2 pr-4">{{ $member['age'] }}歳</td>
                <td class="py-2">{{ $genderLabels[$member['gender']] ?? '' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="flex gap-4">
    <form method="POST" action="{{ route('entry.submit', $event) }}">
        @csrf
        <button type="submit" class="btn btn-primary">申込を確定する</button>
    </form>
    <a href="{{ route('entry.index', $event) }}" class="btn btn-secondary">入力へ戻る</a>
</div>
@endsection
