<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pais;
use App\Models\Lugar;
use App\Models\Coche;
use App\Models\Alquiler;

use Database\Seeders\UserSeeder;
use Database\Seeders\ContinenteSeeder;
use Database\Seeders\TipoLugarSeeder;
use Database\Seeders\CarroceriaCocheSeeder;
use Database\Seeders\MarcaCocheSeeder;
use Database\Seeders\IdiomaSeeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->count(5)->create();

        $this->call([
            UserSeeder::class,
            ContinenteSeeder::class,
            IdiomaSeeder::class,
            TipoLugarSeeder::class,
            CarroceriaCocheSeeder::class,
            MarcaCocheSeeder::class,
        ]);

        Pais::factory()->count(10)->create();
        Lugar::factory()->count(10)->create();
        Coche::factory()->count(20)->create();
        Alquiler::factory()->count(5)->create();
    }
}
