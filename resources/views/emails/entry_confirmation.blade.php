<x-mail::message>
@if($entry->event->email_header)
{{ $entry->event->email_header }}
@else
# 申込受付完了のお知らせ

{{ $entry->event->title }} へのお申込を受け付けました。
@endif

**受付番号: {{ $entry->entry_no }}**

---

## 申込内容

- **時間枠:** {{ $entry->slot->game_date->format('Y年m月d日') }} {{ substr($entry->slot->start_time, 0, 5) }}〜{{ substr($entry->slot->end_time, 0, 5) }}
- **申込者:** {{ $entry->rep_name }}（{{ $entry->rep_phone }}）

### 参加者
@foreach($entry->members as $member)
{{ $member->sort_order }}. {{ $member->name }}（{{ $member->age }}歳・{{ $member->genderLabel() }}）
@endforeach

---

@if($entry->event->email_body)
{{ $entry->event->email_body }}

@endif
申込内容の変更・キャンセルは以下のURLから行えます（イベント受付期間中のみ）。

<x-mail::button :url="route('entry.edit', [$entry->event, $entry->edit_token])">
申込内容を確認・変更する
</x-mail::button>

@if($entry->event->contact_email)
お問合せ: {{ $entry->event->contact_email }}
@endif

@if($entry->event->email_footer)
{{ $entry->event->email_footer }}
@else
ご来場をお待ちしています。
@endif
</x-mail::message>
