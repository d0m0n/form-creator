<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $event = $this->route('event');
        $slot  = $this->route('slot');

        return [
            'game_date'  => ['required', 'date'],
            'name'       => ['required', 'string', 'max:100', Rule::unique('slots')->where('event_id', $event->id)->where('game_date', $this->game_date)->ignore($slot)],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'capacity'   => ['required', 'integer', 'min:1', 'max:999'],
            'is_active'  => ['boolean'],
        ];
    }
}
