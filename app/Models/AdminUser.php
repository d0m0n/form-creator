<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'is_active', 'last_login'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password'   => 'hashed',
            'is_active'  => 'boolean',
            'last_login' => 'datetime',
        ];
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'created_by');
    }
}
