<x-mail::message>
# 申込受付完了のお知らせ

{{ $entry->event->title }} へのお申込を受け付けました。

**受付番号: {{ $entry->entry_no }}**

---

## 申込内容

- **時間枠:** {{ $entry->slot->game_date->format('Y年m月d日') }} {{ $entry->slot->start_time }}〜{{ $entry->slot->end_time }}
- **代表者:** {{ $entry->rep_name }}（{{ $entry->rep_phone }}）

### メンバー
@foreach($entry->members as $member)
{{ $member->sort_order }}. {{ $member->name }}（{{ $member->age }}歳・{{ $member->genderLabel() }}）
@endforeach

---

申込内容の変更・キャンセルは以下のURLから行えます（イベント受付期間中のみ）。

[申込内容を確認・変更する]({{ route('entry.edit', [$entry->event, $entry->edit_token]) }})

@if($entry->event->contact_email)
お問合せ: {{ $entry->event->contact_email }}
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
