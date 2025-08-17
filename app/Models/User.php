<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'api_id',
        'name',
        'last_name',
        'email',
        'password', // <-- AÑADIDO
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password', // <-- AÑADIDO (para seguridad)
        'remember_token',
    ];

    // ... (tu método getRouteKeyName() se queda igual)
}