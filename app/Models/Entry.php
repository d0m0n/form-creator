<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    protected $fillable = [
        'event_id', 'slot_id', 'guest_user_id', 'entry_no', 'edit_token',
        'rep_name', 'rep_age', 'email', 'status',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    public function guestUser()
    {
        return $this->belongsTo(GuestUser::class);
    }

    public function members()
    {
        return $this->hasMany(EntryMember::class)->orderBy('sort_order');
    }

    public function isCancellable(): bool
    {
        return $this->status === 'confirmed' && $this->event->isOpen();
    }
}
