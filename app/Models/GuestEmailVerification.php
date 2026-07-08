<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestEmailVerification extends Model
{
    protected $fillable = ['guest_user_id', 'email', 'verify_token', 'expires_at'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function guestUser()
    {
        return $this->belongsTo(GuestUser::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
