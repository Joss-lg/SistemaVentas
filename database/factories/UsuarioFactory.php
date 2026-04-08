<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'username' => fake()->unique()->userName(),
            // Usamos tu nombre de columna 'password_hash'
            'password_hash' => Hash::make('password'), 
            // Elegimos un rol aleatorio de los que definiste en el enum
            'rol' => fake()->randomElement(['administrador', 'cajero', 'supervisor']),
            'activo' => true,
            // 'fecha_creacion' se llena solo gracias a useCurrent()
            // 'ultima_sesion' queda como null por defecto
        ];
    }
}