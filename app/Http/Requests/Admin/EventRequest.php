<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $event = $this->route('event');

        return [
            'title'         => ['required', 'string', 'max:200'],
            'slug'          => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('events', 'slug')->ignore($event)],
            'start_date'      => ['required', 'date'],
            'end_date'        => ['required', 'date', 'gte:start_date'],
            'entry_deadline'       => ['nullable', 'date'],
            'header_image'         => ['nullable', 'image', 'max:2048'],
            'remove_header_image'  => ['nullable', 'boolean'],
            'description'          => ['nullable', 'string'],
            'notes'         => ['nullable', 'string'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'status'        => ['required', Rule::in(['draft', 'open', 'closed'])],
        ];
    }
}
