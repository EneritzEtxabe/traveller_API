<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="LugarDB",
 *     title="Lugar (BBDD)",
 *     description="Esquema de entrada/salida basado en la tabla 'lugares'",
 *     type="object",
 *     @OA\Property(property="nombre", type="string", example="Cataratas del Iguazú"),
 *     @OA\Property(property="descripcion", type="string", nullable=true, example="Cascadas en la selva tropical de Misiones."),
 *     @OA\Property(property="imagen_url", type="string", format="url", nullable=true, example="https://example.com/cataratas.jpg"),
 *     @OA\Property(property="web_url", type="string", format="url", nullable=true, example="https://turismo.argentina.gob.ar/iguazu"),
 *     @OA\Property(property="localizacion_url", type="string", format="url", nullable=true, example="https://goo.gl/maps/iguazu"),
 *     @OA\Property(property="pais_id", type="integer", example=1, description="ID del país al que pertenece el lugar"),
 *     @OA\Property(
 *         property="tipoLugares",
 *         type="array",
 *         description="IDs de los tipos de lugar relacionados",
 *         @OA\Items(type="integer", example=3)
 *     ),
 * )
 */

class Lugar extends Model
{
    use HasFactory;
    
    protected $table = 'lugares';
    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen_url',
        'web_url',
        'localizacion_url',
        'pais_id'
    ];

    //Relación lugar:pais(N:1)
    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }

    //Relación tipo:lugar (N:N)
    public function tipoLugares()
    {
        return $this->belongsToMany(TipoLugar::class, 'lugar_tipo_rel');
    }
}
