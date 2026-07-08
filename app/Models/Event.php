<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'description', 'slug', 'start_date', 'end_date',
        'entry_deadline', 'header_image', 'member_count', 'contact_email', 'notes', 'status', 'created_by',
        'email_header', 'email_body', 'email_footer',
    ];

    protected function casts(): array
    {
        return [
            'start_date'     => 'date',
            'end_date'       => 'date',
            'entry_deadline' => 'date',
            'member_count'   => 'integer',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(AdminUser::class, 'created_by');
    }

    public function slots()
    {
        return $this->hasMany(Slot::class)->orderBy('game_date')->orderBy('start_time');
    }

    public function activeSlots()
    {
        return $this->slots()->where('is_active', true);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function members()
    {
        return $this->hasManyThrough(EntryMember::class, Entry::class);
    }

    public function guestOwners()
    {
        return $this->belongsToMany(GuestUser::class, 'guest_event_owners');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isDeadlinePassed(): bool
    {
        return $this->entry_deadline !== null
            && $this->entry_deadline->copy()->endOfDay()->isPast();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
