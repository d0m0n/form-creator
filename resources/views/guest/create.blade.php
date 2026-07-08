@extends('layouts.guest')
@section('title', 'イベントフォームを作成')
@section('content')
<h1 class="text-std-28 font-bold mb-6">イベントフォームを作成する</h1>
<p class="mb-6 text-std-16">メールアドレスを確認してからイベントを作成できます。</p>

<div class="card max-w-[480px]">
    <form method="POST" action="{{ route('guest.email.send') }}" class="space-y-5">
        @csrf
        <div class="form-group">
            <label class="form-label" for="email">メールアドレス <span class="badge-required">必須</span></label>
            <input type="email" id="email" name="email" class="form-input" required aria-required="true">
            @error('email')<p class="form-error" role="alert">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn btn-primary">確認メールを送信する</button>
    </form>
</div>
@endsection
