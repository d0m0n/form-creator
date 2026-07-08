<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryMember extends Model
{
    protected $fillable = ['entry_id', 'sort_order', 'name', 'age', 'gender'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'age'        => 'integer',
        ];
    }

    public function genderLabel(): string
    {
        return match ($this->gender) {
            'male'   => '男性',
            'female' => '女性',
            default  => 'その他',
        };
    }

    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }
}
