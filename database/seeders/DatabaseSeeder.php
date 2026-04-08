<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Creamos tu usuario administrador de pruebas principal
        Usuario::factory()->create([
            'nombre' => 'Josue Lazaro',
            'username' => 'admin',
            'password_hash' => Hash::make('admin123'),
            'rol' => 'administrador',
            'activo' => true,
        ]);

        // 2. Generamos 5 usuarios aleatorios adicionales (cajeros o supervisores)
        Usuario::factory(5)->create();
    }
}