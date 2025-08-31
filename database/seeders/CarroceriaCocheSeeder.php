<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\CarroceriaCoche;

class CarroceriaCocheSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carrocerias=['Sedán','Familiar','Cupé','SUV','Monovolumen','Pick-up','Descapotable'];

        foreach($carrocerias as $carroceria){
            CarroceriaCoche::firstOrCreate(['nombre'=>$carroceria]);
        }
    }
}
