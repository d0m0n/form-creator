@extends('layouts.entry')
@section('title', isset($event) ? 'イベント編集' : 'イベント作成')
@section('content')
<h1 class="text-std-28 font-bold mb-8">{{ isset($event) ? 'イベント編集' : 'イベント作成' }}</h1>

@if($errors->any())
<div class="error-summary mb-6" role="alert"><h2 class="error-summary__title">入力エラー</h2><ul class="error-summary__list">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="card max-w-[760px]">
    @if(isset($event))
    <form method="POST" action="{{ route('guest.event.update', $event) }}" class="space-y-6">
        @csrf @method('PATCH')
    @else
    <form method="POST" action="{{ route('guest.event.store') }}" class="space-y-6">
        @csrf
    @endif
        @include('admin.events._form')
        <div class="flex gap-4">
            <button type="submit" class="btn btn-primary">{{ isset($event) ? '保存する' : '作成する' }}</button>
            @isset($event)
            <a href="{{ route('guest.event.show', $event) }}" class="btn btn-secondary">キャンセル</a>
            @endisset
        </div>
    </form>
</div>
@endsection
