<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestUser extends Model
{
    protected $fillable = ['token', 'email', 'email_verified', 'email_verified_at', 'last_seen_at'];

    protected function casts(): array
    {
        return [
            'email_verified'    => 'boolean',
            'email_verified_at' => 'datetime',
            'last_seen_at'      => 'datetime',
        ];
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function eventOwners()
    {
        return $this->hasMany(GuestEventOwner::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'guest_event_owners');
    }

    public function emailVerifications()
    {
        return $this->hasMany(GuestEmailVerification::class);
    }
}
