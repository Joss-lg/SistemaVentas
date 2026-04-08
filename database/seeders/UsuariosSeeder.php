<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        // Crear el Administrador
        Usuario::create([
            'nombre' => 'Admin Sistema',
            'username' => 'admin',
            'password_hash' => Hash::make('admin123'), // La clave será admin123
            'rol' => 'administrador',
            'activo' => true,
        ]);

        // Crear el Cajero
        Usuario::create([
            'nombre' => 'Cajero de Turno',
            'username' => 'cajero',
            'password_hash' => Hash::make('cajero123'), // La clave será cajero123
            'rol' => 'cajero',
            'activo' => true,
        ]);
    }
}
