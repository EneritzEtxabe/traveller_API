<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Http;
use App\Models\Idioma;

class IdiomaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $idiomas=[
            'en'=>'Inglés',
            'eu' =>'Euskera',
            'ar'=>'Árabe',
            'es'=>'Castellano',
            'fr'=>'Francés',
            'de'=>'Alemán',
            'zh'=>'Chino',
        ];
        foreach($idiomas as $iso=>$idioma){
            Idioma::firstOrCreate(['iso_639_1'=>$iso],['nombre'=>$idioma]);
        }
    }
}
