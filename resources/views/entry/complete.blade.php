@extends('layouts.entry')
@section('title', '申込完了')
@php $step = 3; @endphp
@section('content')
<div class="text-center py-12">
    <h1 class="text-std-28 font-bold mb-4">申込が完了しました</h1>
    <p class="text-std-16 mb-2">受付番号: <strong class="text-std-22">{{ session('entry_no') }}</strong></p>
    <p class="text-std-16 text-text-sub">確認メールをお送りしました。届かない場合はお問合せください。</p>
</div>
@endsection
