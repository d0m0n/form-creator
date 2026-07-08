<div class="grid grid-cols-2 gap-4">
    <div class="form-group">
        <label class="form-label" for="game_date">開催日 <span class="badge-required">必須</span></label>
        @php
            $jpDays       = ['日','月','火','水','木','金','土'];
            $selectedDate = old('game_date', isset($slot) ? $slot->game_date->format('Y-m-d') : '');
            $d = $event->start_date->copy();
        @endphp
        <select id="game_date" name="game_date" class="form-select" required>
            <option value="">日付を選択</option>
            @while($d->lte($event->end_date))
            <option value="{{ $d->format('Y-m-d') }}" {{ $selectedDate === $d->format('Y-m-d') ? 'selected' : '' }}>
                {{ $d->format('Y/m/d') }}（{{ $jpDays[$d->dayOfWeek] }}）
            </option>
            @php $d->addDay(); @endwhile
        </select>
        @error('game_date')<p class="form-error" role="alert">{{ $message }}</p>@enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="name">枠名 <span class="badge-required">必須</span></label>
        <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $slot->name ?? '') }}" placeholder="例：午前の部・第1試合" maxlength="100" required>
        @error('name')<p class="form-error" role="alert">{{ $message }}</p>@enderror
    </div>
</div>
<div class="grid grid-cols-2 gap-4">
    <div class="form-group">
        <label class="form-label" for="start_time">開始時刻 <span class="badge-required">必須</span></label>
        <input type="time" id="start_time" name="start_time" class="form-input" value="{{ old('start_time', $slot->start_time ?? '') }}" required>
    </div>
    <div class="form-group">
        <label class="form-label" for="end_time">終了時刻 <span class="badge-required">必須</span></label>
        <input type="time" id="end_time" name="end_time" class="form-input" value="{{ old('end_time', $slot->end_time ?? '') }}" required>
    </div>
</div>
<div class="form-group">
    <label class="form-label" for="capacity">定員 <span class="badge-required">必須</span></label>
    <input type="number" id="capacity" name="capacity" class="form-input w-24" value="{{ old('capacity', $slot->capacity ?? 10) }}" min="1" required>
</div>
<div class="flex items-center gap-2">
    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $slot->is_active ?? true) ? 'checked' : '' }}>
    <label for="is_active" class="text-std-16">公開する</label>
</div>
