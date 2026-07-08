<x-mail::message>
# メールアドレスの確認

イベントフォームの作成にご利用いただきありがとうございます。

以下のボタンを押してメールアドレスを確認してください。

<x-mail::button :url="$verifyUrl">
メールアドレスを確認する
</x-mail::button>

このリンクは **15分間** 有効です。

心当たりのない場合はこのメールを無視してください。

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
