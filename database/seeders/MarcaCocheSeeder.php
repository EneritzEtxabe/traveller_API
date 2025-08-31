<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\MarcaCoche;

class MarcaCocheSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marcas=['Toyota','Volkswagen','BMW','Ford','Audi','Kia','Peugeot','Hyundai','Renault','Nissan'];

        foreach($marcas as $marca){
            MarcaCoche::firstOrCreate(['nombre'=>$marca]);
        }
    }
}
