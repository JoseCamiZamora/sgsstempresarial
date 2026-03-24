<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // El trait de Spatie

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; // Activamos Spatie aquí

    // 👇 AÑADE ESTA LÍNEA 👇
    protected $guard_name = 'web';

    // 1. Actualizamos los campos según tu base de datos
    protected $fillable = [
        'identificacion',
        'tipo',
        'name', // Cambiado de 'name' a 'nombres'
        'email',
        'password',
        'telefono',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    // Opcional: Si tu sistema de login espera 'identificacion' en lugar de 'email'
    // puedes agregar esta función para que Laravel lo sepa:
    // public function username()
    // {
    //     return 'identificacion';
    // }
}
