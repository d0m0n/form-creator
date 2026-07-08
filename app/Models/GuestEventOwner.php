<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestEventOwner extends Model
{
    protected $fillable = ['guest_user_id', 'event_id'];

    public function guestUser()
    {
        return $this->belongsTo(GuestUser::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
