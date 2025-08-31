<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Continente;

class ContinenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $continentes=['Ásia','África','América','Europa','Oceanía','Sudamérica','Norteamérica','Antártida'];

        foreach($continentes as $continente){
            Continente::firstOrCreate(['nombre'=>$continente]);
        }
    }
}
