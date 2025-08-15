<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'api_id', 'name', 'last_name', 'email', 'token'
    ];

    protected $hidden = [
        'token',
    ];

    // Opcional: para usar como identificador en las rutas si lo necesitas
    public function getRouteKeyName()
    {
        return 'api_id';
    }
}
