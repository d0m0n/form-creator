@extends('layouts.guest')
@section('title', '確認メール送信完了')
@section('content')
<div class="text-center py-12">
    <h1 class="text-std-28 font-bold mb-4">確認メールを送信しました</h1>
    <p class="text-std-16 mb-2"><strong>{{ $email }}</strong> 宛にメールを送信しました。</p>
    <p class="text-std-16 text-text-sub">メール内のリンクをクリックしてください（有効期限15分）。</p>
</div>
@endsection
