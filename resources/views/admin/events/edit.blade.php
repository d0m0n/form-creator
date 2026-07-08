@extends('layouts.admin')
@section('title', 'イベント編集')
@section('content')
<h1 class="text-std-28 font-bold mb-8">イベント編集</h1>

@if($errors->any())
<div class="error-summary mb-6" role="alert" tabindex="-1">
    <h2 class="error-summary__title">入力内容にエラーがあります</h2>
    <ul class="error-summary__list">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="card max-w-[760px]">
    <form method="POST" action="{{ route('admin.events.update', $event) }}" class="space-y-6">
        @csrf @method('PATCH')
        @include('admin.events._form')
        <div class="flex gap-4">
            <button type="submit" class="btn btn-primary">保存する</button>
            <a href="{{ route('admin.events.show', $event) }}" class="btn btn-secondary">キャンセル</a>
        </div>
    </form>
</div>
@endsection
