<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfiguracionHardwareSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\ConfiguracionHardware::firstOrCreate(['id' => 1]);
    }
}
