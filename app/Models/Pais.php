<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="PaisDB",
 *     type="object",
 *     title="Pais (BBDD)",
 *     description="Esquema basado en la tabla de base de datos paises",
 *     @OA\Property(property="nombre", type="string", example="Argentina"),
 *     @OA\Property(property="continente_id", type="integer", example=5),
 *     @OA\Property(property="capital", type="string", example="Buenos Aires"),
 *     @OA\Property(property="bandera_url", type="string", format="url", nullable=true, example="https://example.com/bandera.png"),
 *     @OA\Property(property="conduccion", type="string", enum={"izquierda","derecha"}, nullable=true, example="izquierda"),
*      @OA\Property(
*         property="idiomas",
*         type="array",
*         description="IDs de los idiomas del país",
*         @OA\Items(type="integer", example=3)
*     ),
 * )
 */

class Pais extends Model
{
    use HasFactory;
    
    protected $table = 'paises';
    protected $fillable = [
        'nombre',
        'capital',
        'bandera_url',
        'conduccion',
        'continente_id',
    ];

    //Relación N:1
    public function continente()
    {
        return $this->belongsTo(Continente::class);
    }

    //Relación N:N
    public function idiomas()
    {
        return $this->belongsToMany(Idioma::class,'idioma_pais_rel','pais_id','idioma_id');
    }

    // Relación 1:N
    public function lugares()
    {
        return $this->hasMany(Lugar::class);
    }

    // Relación 1:N
    public function coches()
    {
        return $this->hasMany(Coche::class);
    }
}
