<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    protected $fillable = ['event_id', 'game_date', 'name', 'start_time', 'end_time', 'capacity', 'is_active'];

    protected function casts(): array
    {
        return [
            'game_date' => 'date',
            'is_active' => 'boolean',
            'capacity'  => 'integer',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function confirmedEntriesCount(): int
    {
        return $this->entries()->where('status', 'confirmed')->count();
    }

    public function remainingCapacity(): int
    {
        return $this->capacity - $this->confirmedEntriesCount();
    }

    public function isFull(): bool
    {
        return $this->remainingCapacity() <= 0;
    }
}
