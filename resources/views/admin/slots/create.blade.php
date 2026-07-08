@extends('layouts.admin')
@section('title', '時間枠追加')
@section('content')
<h1 class="text-std-28 font-bold mb-2">時間枠追加</h1>
<p class="text-text-sub mb-8">{{ $event->title }}</p>

@if($errors->any())
<div class="error-summary mb-6" role="alert"><h2 class="error-summary__title">入力エラー</h2><ul class="error-summary__list">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="card max-w-[560px]">
    <form method="POST" action="{{ route('admin.slots.store', $event) }}" class="space-y-5">
        @csrf
        @include('admin.slots._form')
        <div class="flex gap-4">
            <button type="submit" class="btn btn-primary">追加する</button>
            <a href="{{ route('admin.events.show', $event) }}" class="btn btn-secondary">戻る</a>
        </div>
    </form>
</div>
@endsection
