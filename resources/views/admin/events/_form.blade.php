<div class="form-group">
    <label class="form-label" for="title">イベント名 <span class="badge-required">必須</span></label>
    <input type="text" id="title" name="title" class="form-input {{ $errors->has('title') ? 'border-error' : '' }}" value="{{ old('title', $event->title ?? '') }}" required aria-required="true">
    @error('title')<p class="form-error" role="alert">{{ $message }}</p>@enderror
</div>
<div class="form-group">
    <label class="form-label" for="slug">スラッグ（URL） <span class="badge-required">必須</span></label>
    <input type="text" id="slug" name="slug" class="form-input" value="{{ old('slug', $event->slug ?? '') }}" placeholder="summer-game-2025" required aria-required="true">
    @error('slug')<p class="form-error" role="alert">{{ $message }}</p>@enderror
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="form-group">
        <label class="form-label" for="start_date">開催開始日 <span class="badge-required">必須</span></label>
        <input type="date" id="start_date" name="start_date" class="form-input" value="{{ old('start_date', isset($event) ? $event->start_date->format('Y-m-d') : '') }}" required>
        @error('start_date')<p class="form-error" role="alert">{{ $message }}</p>@enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="end_date">開催終了日 <span class="badge-required">必須</span></label>
        <input type="date" id="end_date" name="end_date" class="form-input" value="{{ old('end_date', isset($event) ? $event->end_date->format('Y-m-d') : '') }}" required>
        @error('end_date')<p class="form-error" role="alert">{{ $message }}</p>@enderror
    </div>
</div>
<div class="form-group">
    <label class="form-label" for="entry_deadline">申込期限</label>
    <input type="date" id="entry_deadline" name="entry_deadline" class="form-input w-52"
        value="{{ old('entry_deadline', isset($event) && $event->entry_deadline ? $event->entry_deadline->format('Y-m-d') : '') }}">
    <p class="text-std-16 text-text-sub mt-1">設定しない場合はステータスの切り替えで受付を管理します。期限日の終日（23:59）まで受付を許可します。</p>
    @error('entry_deadline')<p class="form-error" role="alert">{{ $message }}</p>@enderror
</div>
<div class="form-group">
    <label class="form-label" for="header_image">ヘッダー画像</label>

    {{-- 現在の画像プレビュー（編集時） --}}
    @if(isset($event) && $event->header_image)
    <div class="mb-3" id="current-image-wrap">
        <img src="{{ Storage::disk('public')->url($event->header_image) }}"
             alt="現在のヘッダー画像"
             class="rounded-md object-cover w-full"
             style="max-height:200px; max-width:760px;">
        <label class="flex items-center gap-2 mt-2 text-std-16 cursor-pointer">
            <input type="checkbox" name="remove_header_image" value="1"
                   id="remove_header_image"
                   onchange="document.getElementById('current-image-wrap').style.opacity = this.checked ? '0.4' : '1'">
            現在の画像を削除する
        </label>
    </div>
    @endif

    {{-- 新規アップロード --}}
    <input type="file" id="header_image" name="header_image"
           class="block w-full text-std-16 file:mr-4 file:py-2 file:px-4
                  file:rounded-sm file:border file:border-border
                  file:text-std-16 file:font-bold file:cursor-pointer
                  file:bg-surface file:text-text hover:file:bg-background"
           accept="image/jpeg,image/png,image/webp,image/gif">
    <p class="text-std-16 text-text-sub mt-1">JPEG・PNG・WebP・GIF、最大 2 MB。推奨横幅 760 px 以上。</p>
    @error('header_image')<p class="form-error" role="alert">{{ $message }}</p>@enderror

    {{-- 選択後プレビュー --}}
    <div id="new-image-preview" class="mt-3 hidden">
        <p class="text-std-14 text-text-sub mb-1">アップロード後のプレビュー</p>
        <img id="new-image-preview-img" src="" alt="" class="rounded-md object-cover w-full" style="max-height:200px; max-width:760px;">
    </div>

    <script>
    document.getElementById('header_image')?.addEventListener('change', function () {
        const file = this.files[0];
        const wrap = document.getElementById('new-image-preview');
        const img  = document.getElementById('new-image-preview-img');
        if (file && file.type.startsWith('image/')) {
            img.src = URL.createObjectURL(file);
            wrap.classList.remove('hidden');
        } else {
            wrap.classList.add('hidden');
        }
    });
    </script>
</div>
<div class="form-group">
    <label class="form-label" for="status">ステータス <span class="badge-required">必須</span></label>
    <select id="status" name="status" class="form-select w-48">
        @foreach(['draft'=>'非公開','open'=>'受付中','closed'=>'受付終了'] as $val => $label)
        <option value="{{ $val }}" {{ old('status', $event->status ?? 'draft') === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label class="form-label" for="description">イベント説明</label>
    <textarea id="description" name="description" class="form-input h-32" rows="4">{{ old('description', $event->description ?? '') }}</textarea>
</div>
<div class="form-group">
    <label class="form-label" for="notes">注意事項</label>
    <textarea id="notes" name="notes" class="form-input h-32" rows="4">{{ old('notes', $event->notes ?? '') }}</textarea>
</div>
<div class="form-group">
    <label class="form-label" for="contact_email">問合せ先メール</label>
    <input type="email" id="contact_email" name="contact_email" class="form-input" value="{{ old('contact_email', $event->contact_email ?? '') }}">
</div>

<div class="mt-8 pt-6 border-t border-border">
    <h2 class="text-std-18 font-bold mb-1">確認メール設定</h2>
    <p class="text-std-16 text-text-sub mb-5">申込者へ送信する確認メールの文章をカスタマイズできます。空白のままにするとデフォルト文章が使われます。</p>
    <div class="space-y-5">
        <div class="form-group">
            <label class="form-label" for="email_header">冒頭文</label>
            <textarea id="email_header" name="email_header" class="form-input h-28" rows="3"
                placeholder="例：○○大会へのお申込ありがとうございます。受付が完了しましたので内容をご確認ください。">{{ old('email_header', $event->email_header ?? '') }}</textarea>
            <p class="text-std-14 text-text-sub mt-1">メール冒頭に表示されます。空白の場合「[イベント名] へのお申込を受け付けました。」が使われます。</p>
        </div>
        <div class="form-group">
            <label class="form-label" for="email_body">追加メッセージ</label>
            <textarea id="email_body" name="email_body" class="form-input h-28" rows="3"
                placeholder="例：当日は開始時刻の10分前までに受付をお済ませください。">{{ old('email_body', $event->email_body ?? '') }}</textarea>
            <p class="text-std-14 text-text-sub mt-1">申込内容の後に表示されます。注意事項・持ち物・当日案内などにご利用ください。</p>
        </div>
        <div class="form-group">
            <label class="form-label" for="email_footer">結び文</label>
            <textarea id="email_footer" name="email_footer" class="form-input h-24" rows="2"
                placeholder="例：ご不明な点がございましたらお気軽にお問合せください。">{{ old('email_footer', $event->email_footer ?? '') }}</textarea>
            <p class="text-std-14 text-text-sub mt-1">メール末尾に表示されます。空白の場合「Thanks, [システム名]」が使われます。</p>
        </div>
    </div>
</div>
