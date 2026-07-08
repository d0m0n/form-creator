@extends('layouts.entry')
@section('title', '申込編集')
@section('content')
<h1 class="text-std-28 font-bold mb-6">申込内容の変更</h1>

@if(session('success'))
<div class="p-4 mb-6 bg-success-bg text-success rounded-md" role="alert">{{ session('success') }}</div>
@endif

<div class="card mb-6 text-std-16">
    <p>受付番号: <strong>{{ $entry->entry_no }}</strong></p>
</div>

<form method="POST" action="{{ route('entry.update', [$event, $token]) }}" novalidate class="space-y-6 mb-8">
    @csrf
    <fieldset>
        <legend class="form-label mb-3">希望時間枠 <span class="badge-required">必須</span></legend>
        @foreach($slots as $slot)
        @php $remaining = $slot->capacity - $slot->confirmed_count + ($slot->id === $entry->slot_id ? 1 : 0); @endphp
        <label class="radio-card mb-3 {{ $remaining <= 0 ? 'is-disabled' : '' }}">
            <input type="radio" name="slot_id" value="{{ $slot->id }}"
                {{ $entry->slot_id == $slot->id ? 'checked' : '' }}
                {{ $remaining <= 0 ? 'disabled' : '' }}>
            <span class="radio-card__label">
                <span class="radio-card__time">{{ $slot->start_time }}〜{{ $slot->end_time }}</span>
                <span class="radio-card__game">{{ $slot->game_date->format('m/d') }} {{ $slot->name }}</span>
                @if($remaining <= 0)<span class="badge-full">満員</span>
                @else<span class="badge-remain">残り{{ $remaining }}名</span>@endif
            </span>
        </label>
        @endforeach
    </fieldset>

    <div class="form-group">
        <label class="form-label" for="rep_name">代表者氏名 <span class="badge-required">必須</span></label>
        <input type="text" id="rep_name" name="rep_name" class="form-input" value="{{ old('rep_name', $entry->rep_name) }}" required>
    </div>
    <div class="form-group">
        <label class="form-label" for="rep_phone">電話番号 <span class="badge-required">必須</span></label>
        <input type="tel" id="rep_phone" name="rep_phone" class="form-input w-52"
               value="{{ old('rep_phone', $entry->rep_phone) }}" placeholder="例：090-1234-5678"
               autocomplete="tel" required>
    </div>
    <div class="form-group">
        <label class="form-label" for="email">メールアドレス <span class="badge-required">必須</span></label>
        <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $entry->email) }}" required>
    </div>
    <div class="form-group">
        <label class="form-label" for="email_confirmation">メールアドレス（確認） <span class="badge-required">必須</span></label>
        <input type="email" id="email_confirmation" name="email_confirmation" class="form-input" required>
    </div>

    @foreach($entry->members as $i => $member)
    <fieldset class="p-4 border border-border rounded-md">
        <legend class="font-bold text-std-16 px-2">参加者 {{ $i+1 }}</legend>
        <div class="grid grid-cols-1 gap-4 mt-2 md:grid-cols-3">
            <div class="form-group md:col-span-1">
                <label class="form-label">氏名 <span class="badge-required">必須</span></label>
                <input type="text" name="members[{{ $i }}][name]" class="form-input"
                       value="{{ old("members.{$i}.name", $member->name) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">年齢 <span class="badge-required">必須</span></label>
                <input type="number" name="members[{{ $i }}][age]" class="form-input"
                       value="{{ old("members.{$i}.age", $member->age) }}" min="1" max="99" required>
            </div>
            <div class="form-group">
                <label class="form-label">性別 <span class="badge-required">必須</span></label>
                <select name="members[{{ $i }}][gender]" class="form-select" required>
                    <option value="">選択してください</option>
                    <option value="male"   {{ old("members.{$i}.gender", $member->gender) === 'male'   ? 'selected' : '' }}>男性</option>
                    <option value="female" {{ old("members.{$i}.gender", $member->gender) === 'female' ? 'selected' : '' }}>女性</option>
                    <option value="other"  {{ old("members.{$i}.gender", $member->gender) === 'other'  ? 'selected' : '' }}>その他</option>
                </select>
            </div>
        </div>
    </fieldset>
    @endforeach

    <button type="submit" class="btn btn-primary">変更を保存する</button>
</form>

<form method="POST" action="{{ route('entry.cancel', [$event, $token]) }}" onsubmit="return confirm('申込をキャンセルしますか？')">
    @csrf
    <button type="submit" class="text-error underline cursor-pointer bg-transparent border-0 text-std-16">申込をキャンセルする</button>
</form>
@endsection
