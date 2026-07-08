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
<div class="grid grid-cols-2 gap-4">
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
