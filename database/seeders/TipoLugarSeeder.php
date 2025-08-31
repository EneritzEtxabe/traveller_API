<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\TipoLugar;

class TipoLugarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos=['Cultura','Natura','Gastronomía','Deporte','Salud y Bienestas','Rural','Aventura','Playa','Montaña', 'Ciudad'];

        foreach($tipos as $tipo){
            TipoLugar::firstOrCreate(['nombre'=>$tipo]);
        }
    }
}
