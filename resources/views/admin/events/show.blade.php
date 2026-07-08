@extends('layouts.admin')
@section('title', $event->title)
@section('content')

{{-- イベントヘッダー --}}
<div class="mb-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-std-28 font-bold">{{ $event->title }}</h1>
            <p class="text-text-sub mt-1">
                {{ ['draft'=>'非公開','open'=>'受付中','closed'=>'受付終了'][$event->status] }}
                ／ {{ $event->start_date->format('Y/m/d') }} 〜 {{ $event->end_date->format('Y/m/d') }}
                @if($event->entry_deadline)
                ／ 申込期限：{{ $event->entry_deadline->format('Y/m/d') }}
                @if($event->isDeadlinePassed())
                    <span class="inline-block text-std-14 font-bold text-error ml-1">（期限切れ）</span>
                @endif
                @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-secondary">編集</a>
            <a href="{{ route('admin.entries.index', $event) }}" class="btn btn-secondary">申込一覧</a>
            <a href="{{ route('admin.export.entries', $event) }}" class="btn btn-secondary">CSVダウンロード</a>
        </div>
    </div>
</div>

{{-- フォームURL --}}
@if($event->isOpen())
<div class="card mb-6">
    <p class="text-std-14 text-text-sub mb-1">申込フォームURL（コピーして参加者に共有してください）</p>
    <div class="flex items-center gap-3">
        <code class="flex-1 text-std-16 font-bold break-all bg-background px-3 py-2 rounded-sm" id="form-url">{{ route('entry.index', $event) }}</code>
        <button type="button"
            onclick="navigator.clipboard.writeText(document.getElementById('form-url').textContent).then(()=>this.textContent='コピー済み')"
            class="btn btn-secondary" style="min-width:auto;padding:8px 16px;">コピー</button>
    </div>
</div>
@endif

{{-- 登録済み時間枠 --}}
<h2 class="text-std-22 font-bold mb-4">登録済み時間枠</h2>

<div class="card overflow-x-auto mb-8">
    <table class="w-full text-std-16">
        <thead>
            <tr class="border-b border-border text-left text-text-sub">
                <th class="py-3 pr-4 font-bold">開催日</th>
                <th class="py-3 pr-4 font-bold">開始</th>
                <th class="py-3 pr-4 font-bold">終了</th>
                <th class="py-3 pr-4 font-bold">定員</th>
                <th class="py-3 pr-4 font-bold">申込数</th>
                <th class="py-3 pr-4 font-bold">公開</th>
                <th class="py-3 font-bold">操作</th>
            </tr>
        </thead>
        <tbody>
        @forelse($slots as $slot)
            <tr class="border-b border-border {{ !$slot->is_active ? 'opacity-50' : '' }}">
                <td class="py-3 pr-4">
                    {{ $slot->game_date->format('Y/m/d') }}
                    <span class="text-text-sub text-std-14">{{ $slot->name }}</span>
                </td>
                <td class="py-3 pr-4">{{ $slot->start_time }}</td>
                <td class="py-3 pr-4">{{ $slot->end_time }}</td>
                <td class="py-3 pr-4">{{ $slot->capacity }}名</td>
                <td class="py-3 pr-4">{{ $slot->entries_count }}名</td>
                <td class="py-3 pr-4">{{ $slot->is_active ? '公開' : '非公開' }}</td>
                <td class="py-3 flex gap-3">
                    <a href="{{ route('admin.slots.edit', [$event, $slot]) }}" class="text-link underline">編集</a>
                    <form method="POST" action="{{ route('admin.slots.destroy', [$event, $slot]) }}" onsubmit="return confirm('削除しますか？')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-error underline cursor-pointer bg-transparent border-0">削除</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-text-sub">
                    まだ時間枠がありません。下のフォームから追加してください。
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- 時間枠追加フォーム --}}
<h2 class="text-std-22 font-bold mb-4">時間枠を追加</h2>

<div class="card">
    <form method="POST" action="{{ route('admin.slots.bulk', $event) }}" id="bulk-form">
        @csrf

        @if($errors->any())
        <div class="error-summary mb-5" role="alert">
            <p class="error-summary__title">入力内容を確認してください</p>
            <ul class="error-summary__list">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <div class="overflow-x-auto -mx-4 px-4 md:mx-0 md:px-0">
        <div class="min-w-[580px]">

        {{-- カラムヘッダー（md以上で表示） --}}
        <div class="hidden md:grid gap-3 mb-2 text-std-14 font-bold text-text-sub px-1"
             style="grid-template-columns:170px 1fr 110px 110px 90px 40px;">
            <span>開催日</span>
            <span>枠名</span>
            <span>開始時刻</span>
            <span>終了時刻</span>
            <span>定員（名）</span>
            <span></span>
        </div>

        {{-- 開催期間の日付リストを生成 --}}
        @php
            $jpDays    = ['日','月','火','水','木','金','土'];
            $eventDates = [];
            $d = $event->start_date->copy();
            while ($d->lte($event->end_date)) {
                $eventDates[] = $d->copy();
                $d->addDay();
            }
        @endphp

        {{-- 入力行コンテナ --}}
        <div id="slot-rows" class="space-y-3">
            <div class="slot-row grid gap-3 items-center"
                 style="grid-template-columns:170px 1fr 110px 110px 90px 40px;">
                <select name="slots[0][game_date]" class="form-select" required aria-label="開催日">
                    <option value="">日付を選択</option>
                    @foreach($eventDates as $d)
                    <option value="{{ $d->format('Y-m-d') }}">{{ $d->format('m/d') }}（{{ $jpDays[$d->dayOfWeek] }}）</option>
                    @endforeach
                </select>
                <input type="text" name="slots[0][name]"
                       class="form-input" placeholder="例：午前の部" maxlength="100" required aria-label="枠名">
                <input type="time" name="slots[0][start_time]"
                       class="form-input" required aria-label="開始時刻">
                <input type="time" name="slots[0][end_time]"
                       class="form-input" required aria-label="終了時刻">
                <input type="number" name="slots[0][capacity]"
                       class="form-input" value="10" min="1" required aria-label="定員">
                <button type="button" class="remove-row flex items-center justify-center
                        w-9 h-10 rounded-sm border border-border text-text-sub
                        hover:text-error hover:border-error transition-colors
                        cursor-pointer bg-surface disabled:opacity-30 disabled:cursor-not-allowed"
                        disabled aria-label="この行を削除">×</button>
            </div>
        </div>

        </div>{{-- /min-w --}}
        </div>{{-- /overflow-x-auto --}}

        <div class="flex flex-wrap items-center gap-3 mt-5">
            <button type="button" id="add-row" class="btn btn-secondary" style="min-width:auto;">
                + 行を追加
            </button>
            <button type="submit" class="btn btn-primary">登録する</button>
        </div>
    </form>
</div>

<script>
(function () {
    const container = document.getElementById('slot-rows');

    function reindex() {
        container.querySelectorAll('.slot-row').forEach((row, i) => {
            row.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/slots\[\d+\]/, `slots[${i}]`);
            });
        });
    }

    function syncButtons() {
        const rows = container.querySelectorAll('.slot-row');
        rows.forEach(row => {
            row.querySelector('.remove-row').disabled = rows.length === 1;
        });
    }

    document.getElementById('add-row').addEventListener('click', () => {
        const rows   = container.querySelectorAll('.slot-row');
        const clone  = rows[rows.length - 1].cloneNode(true);
        const newIdx = rows.length;

        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/slots\[\d+\]/, `slots[${newIdx}]`);
            // テキスト・時刻入力のみリセット（定員・日付プルダウンはそのまま引き継ぐ）
            if (el.tagName === 'INPUT' && !el.name.includes('capacity') && el.type !== 'time') {
                el.value = '';
            }
        });
        clone.querySelector('.remove-row').disabled = false;
        container.appendChild(clone);
        clone.querySelector('input, select').focus();
        syncButtons();
    });

    container.addEventListener('click', e => {
        const btn = e.target.closest('.remove-row');
        if (btn && !btn.disabled) {
            btn.closest('.slot-row').remove();
            reindex();
            syncButtons();
        }
    });
})();
</script>

@endsection
