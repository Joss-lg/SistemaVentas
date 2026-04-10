<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $table = 'usuarios';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'username', 'password_hash', 'rol', 'activo','tema'
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function ventas(): HasMany {
        return $this->hasMany(Venta::class, 'usuario_id');
    }

    public function esAdmin(): bool {
        return $this->rol === 'administrador';
    }

    public function getAuthPassword() {
        return $this->password_hash;
    }
}