<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $event = $this->route('event');

        return [
            'slot_id'              => ['required', 'integer', Rule::exists('slots', 'id')->where('event_id', $event->id)->where('is_active', true)],
            'rep_name'             => ['required', 'string', 'max:100'],
            'rep_phone'            => ['required', 'string', 'max:20', 'regex:/^[0-9\-]+$/'],
            'email'                => ['required', 'email', 'max:255'],
            'email_confirmation'   => ['required', 'same:email'],
            'members'              => ['required', 'array', 'min:1', "max:{$event->member_count}"],
            'members.*.name'       => ['required', 'string', 'max:100'],
            'members.*.age'        => ['required', 'integer', 'min:1', 'max:99'],
            'members.*.gender'     => ['required', 'in:male,female,other'],
        ];
    }

    public function attributes(): array
    {
        return [
            'slot_id'            => '時間枠',
            'rep_name'           => '代表者氏名',
            'rep_phone'          => '電話番号',
            'email'              => 'メールアドレス',
            'email_confirmation' => 'メールアドレス（確認）',
            'members.*.name'     => 'メンバー氏名',
            'members.*.age'      => '年齢',
            'members.*.gender'   => '性別',
        ];
    }
}
