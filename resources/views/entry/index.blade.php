@extends('layouts.entry')
@section('title', $event->title)
@php $step = 1; @endphp
@section('content')

@if($event->description)
<div class="mb-6 text-std-16 leading-[1.7]">{!! nl2br(e($event->description)) !!}</div>
@endif

@if($deadlinePassed ?? false)
{{-- 申込期限切れ --}}
<div class="p-5 mb-8 rounded-md border-2 border-error bg-[color-mix(in_srgb,var(--color-error)_8%,white)]" role="alert">
    <p class="font-bold text-error text-std-18 mb-1">受付を締め切りました</p>
    <p class="text-std-16">申込期限（{{ $event->entry_deadline->format('Y年m月d日') }}）を過ぎているため、現在この申込フォームは受付を終了しています。</p>
    @if($event->contact_email)
    <p class="text-std-16 mt-2">お問合せは <a href="mailto:{{ $event->contact_email }}" class="text-link underline">{{ $event->contact_email }}</a> までご連絡ください。</p>
    @endif
</div>
@elseif($allFull ?? false)
{{-- 全枠満員 --}}
<div class="p-5 mb-8 rounded-md border-2 border-[var(--color-warning,#b45309)] bg-[color-mix(in_srgb,#b45309_8%,white)]" role="alert">
    <p class="font-bold text-std-18 mb-1" style="color:#b45309;">現在満員です</p>
    <p class="text-std-16">すべての時間枠が満員のため、現在お申込みを受け付けていません。</p>
    @if($event->contact_email)
    <p class="text-std-16 mt-2">お問合せは <a href="mailto:{{ $event->contact_email }}" class="text-link underline">{{ $event->contact_email }}</a> までご連絡ください。</p>
    @endif
</div>
@else

@if($errors->any())
<div class="error-summary mb-6" role="alert" tabindex="-1">
    <h2 class="error-summary__title">入力内容にエラーがあります</h2>
    <ul class="error-summary__list">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('entry.confirm', $event) }}" novalidate>
    @csrf

    {{-- 時間枠選択 --}}
    <fieldset class="mb-8">
        <legend class="form-label mb-3">希望時間枠 <span class="badge-required">必須</span></legend>
        @forelse($slots as $slot)
        @php $remaining = $slot->capacity - $slot->confirmed_count; @endphp
        <label class="radio-card mb-3 {{ $remaining <= 0 ? 'is-disabled' : '' }}">
            <input type="radio" name="slot_id" value="{{ $slot->id }}"
                {{ old('slot_id') == $slot->id ? 'checked' : '' }}
                {{ $remaining <= 0 ? 'disabled' : '' }}
                aria-label="{{ $slot->game_date->format('Y/m/d') }}{{ $slot->start_time }}〜{{ $slot->end_time }} 残り{{ $remaining }}名">
            <span class="radio-card__label">
                <span class="radio-card__time">{{ $slot->start_time }}〜{{ $slot->end_time }}</span>
                <span class="radio-card__game">{{ $slot->game_date->format('m/d') }} {{ $slot->name }}</span>
                @if($remaining <= 0)
                    <span class="badge-full">満員</span>
                @else
                    <span class="badge-remain">残り{{ $remaining }}名</span>
                @endif
            </span>
        </label>
        @empty
        <p class="text-text-sub">現在受付可能な時間枠がありません。</p>
        @endforelse
        @error('slot_id')<p class="form-error mt-2" role="alert">{{ $message }}</p>@enderror
    </fieldset>

    {{-- 代表者情報 --}}
    <h2 class="text-std-22 font-bold mb-4">代表者情報</h2>
    <div class="space-y-5 mb-8">
        <div class="form-group">
            <label class="form-label" for="rep_name">代表者氏名 <span class="badge-required">必須</span></label>
            <input type="text" id="rep_name" name="rep_name" class="form-input {{ $errors->has('rep_name') ? 'border-error' : '' }}"
                value="{{ old('rep_name') }}" autocomplete="name" required aria-required="true" aria-describedby="{{ $errors->has('rep_name') ? 'rep_name-error' : '' }}">
            @error('rep_name')<p id="rep_name-error" class="form-error" role="alert">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="rep_age">代表者年齢 <span class="badge-required">必須</span></label>
            <input type="number" id="rep_age" name="rep_age" class="form-input w-24" value="{{ old('rep_age') }}" min="1" max="99" required aria-required="true">
            @error('rep_age')<p class="form-error" role="alert">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="email">メールアドレス <span class="badge-required">必須</span></label>
            <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" autocomplete="email" required aria-required="true">
            @error('email')<p class="form-error" role="alert">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="email_confirmation">メールアドレス（確認） <span class="badge-required">必須</span></label>
            <input type="email" id="email_confirmation" name="email_confirmation" class="form-input" autocomplete="email" required aria-required="true">
            @error('email_confirmation')<p class="form-error" role="alert">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- メンバー情報 --}}
    <h2 class="text-std-22 font-bold mb-2">参加者情報</h2>
    <p class="text-std-16 text-text-sub mb-4">最大{{ $event->member_count }}名まで追加できます。</p>

    <div id="member-list" class="space-y-4 mb-4">
        {{-- バリデーションエラー時は old() の件数ぶん復元、初回は1件 --}}
        @php
            $oldMembers = old('members', [['name'=>'','age'=>'','gender'=>'']]);
        @endphp
        @foreach($oldMembers as $i => $oldMember)
        <fieldset class="member-item p-4 border border-border rounded-md relative">
            <legend class="font-bold text-std-16 px-2">参加者 <span class="member-num">{{ $i + 1 }}</span></legend>
            <button type="button" class="remove-member absolute top-3 right-3 text-text-sub hover:text-error transition-colors bg-transparent border-0 cursor-pointer text-std-16 leading-none" aria-label="この参加者を削除">×</button>
            <div class="grid grid-cols-1 gap-4 mt-2 md:grid-cols-3">
                <div class="form-group md:col-span-1">
                    <label class="form-label" for="member_{{ $i }}_name">氏名 <span class="badge-required">必須</span></label>
                    <input type="text" id="member_{{ $i }}_name" name="members[{{ $i }}][name]"
                           class="form-input" value="{{ $oldMember['name'] ?? '' }}" required aria-required="true">
                    @error("members.{$i}.name")<p class="form-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="member_{{ $i }}_age">年齢 <span class="badge-required">必須</span></label>
                    <input type="number" id="member_{{ $i }}_age" name="members[{{ $i }}][age]"
                           class="form-input" value="{{ $oldMember['age'] ?? '' }}" min="1" max="99" required aria-required="true">
                    @error("members.{$i}.age")<p class="form-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="member_{{ $i }}_gender">性別 <span class="badge-required">必須</span></label>
                    <select id="member_{{ $i }}_gender" name="members[{{ $i }}][gender]" class="form-select" required aria-required="true">
                        <option value="">選択してください</option>
                        <option value="male"   {{ ($oldMember['gender'] ?? '') === 'male'   ? 'selected' : '' }}>男性</option>
                        <option value="female" {{ ($oldMember['gender'] ?? '') === 'female' ? 'selected' : '' }}>女性</option>
                        <option value="other"  {{ ($oldMember['gender'] ?? '') === 'other'  ? 'selected' : '' }}>その他</option>
                    </select>
                    @error("members.{$i}.gender")<p class="form-error" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>
        </fieldset>
        @endforeach
    </div>

    <button type="button" id="add-member" class="btn btn-secondary mb-8">+ 参加者を追加</button>

    <script>
    (function () {
        const list    = document.getElementById('member-list');
        const addBtn  = document.getElementById('add-member');
        const maxCount = {{ $event->member_count }};

        function reindex() {
            list.querySelectorAll('.member-item').forEach((item, i) => {
                item.querySelector('.member-num').textContent = i + 1;
                item.querySelectorAll('[name]').forEach(el => {
                    el.name = el.name.replace(/members\[\d+\]/, `members[${i}]`);
                });
                item.querySelectorAll('[id]').forEach(el => {
                    el.id = el.id.replace(/member_\d+_/, `member_${i}_`);
                });
                item.querySelectorAll('[for]').forEach(el => {
                    el.htmlFor = el.htmlFor.replace(/member_\d+_/, `member_${i}_`);
                });
            });
        }

        function syncAddButton() {
            const count = list.querySelectorAll('.member-item').length;
            addBtn.disabled = count >= maxCount;
            addBtn.classList.toggle('opacity-50', count >= maxCount);
            addBtn.classList.toggle('cursor-not-allowed', count >= maxCount);
        }

        function syncRemoveButtons() {
            const items = list.querySelectorAll('.member-item');
            items.forEach(item => {
                item.querySelector('.remove-member').style.display = items.length > 1 ? '' : 'none';
            });
        }

        addBtn.addEventListener('click', () => {
            if (list.querySelectorAll('.member-item').length >= maxCount) return;
            const idx   = list.querySelectorAll('.member-item').length;
            const tmpl  = list.querySelector('.member-item').cloneNode(true);

            tmpl.querySelector('.member-num').textContent = idx + 1;
            tmpl.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/members\[\d+\]/, `members[${idx}]`);
                if (el.tagName === 'INPUT') el.value = '';
                if (el.tagName === 'SELECT') el.value = '';
            });
            tmpl.querySelectorAll('[id]').forEach(el => {
                el.id = el.id.replace(/member_\d+_/, `member_${idx}_`);
            });
            tmpl.querySelectorAll('[for]').forEach(el => {
                el.htmlFor = el.htmlFor.replace(/member_\d+_/, `member_${idx}_`);
            });
            tmpl.querySelector('.remove-member').style.display = '';

            list.appendChild(tmpl);
            tmpl.querySelector('input').focus();
            syncAddButton();
            syncRemoveButtons();
        });

        list.addEventListener('click', e => {
            const btn = e.target.closest('.remove-member');
            if (!btn) return;
            if (list.querySelectorAll('.member-item').length <= 1) return;
            btn.closest('.member-item').remove();
            reindex();
            syncAddButton();
            syncRemoveButtons();
        });

        syncRemoveButtons();
        syncAddButton();
    })();
    </script>

    @if($event->notes)
    <div class="p-4 mb-8 bg-background border border-border rounded-md text-std-16">{!! nl2br(e($event->notes)) !!}</div>
    @endif

    <button type="submit" class="btn btn-primary">確認画面へ進む</button>
</form>
@endif
@endsection
