<x-mail::message>
# 新規申込のお知らせ

{{ $entry->event->title }} に新しい申込がありました。

**受付番号: {{ $entry->entry_no }}**

- **時間枠:** {{ $entry->slot->game_date->format('Y年m月d日') }}
- **代表者:** {{ $entry->rep_name }} / {{ $entry->email }}
- **申込日時:** {{ $entry->created_at->format('Y/m/d H:i') }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
