<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="CocheDB",
 *     type="object",
 *     title="Coche (BBDD)",
 *     description="Modelo que representa un coche con sus relaciones",
 *     required={"marca_id", "carroceria_id", "pais_id", "ano", "nPlazas", "cambio", "estado", "costeDia"},
 *     
 *     @OA\Property(
 *         property="marca_id",
 *         type="integer",
 *         example=2,
 *         description="ID de la marca asociada al coche"
 *     ),
 *     @OA\Property(
 *         property="carroceria_id",
 *         type="integer",
 *         example=1,
 *         description="ID del tipo de carrocería"
 *     ),
 *     @OA\Property(
 *         property="pais_id",
 *         type="integer",
 *         example=4,
 *         description="ID del país al que pertenece el coche"
 *     ),
 *     @OA\Property(
 *         property="ano",
 *         type="integer",
 *         example=2020,
 *         description="Año del coche"
 *     ),
 *     @OA\Property(
 *         property="nPlazas",
 *         type="integer",
 *         example=5,
 *         description="Número de plazas del coche"
 *     ),
 *     @OA\Property(
 *         property="cambio",
 *         type="string",
 *         example="manual",
 *         description="Tipo de cambio del coche (manual o automático)"
 *     ),
 *     @OA\Property(
 *         property="estado",
 *         type="string",
 *         example="disponible",
 *         description="Estado actual del coche (disponible/mantenimiento)"
 *     ),
 *     @OA\Property(
 *         property="costeDia",
 *         type="number",
 *         format="float",
 *         example=35.5,
 *         description="Coste por día del alquiler del coche"
 *     ),
 * )
 */
class Coche extends Model
{
    use HasFactory;
    
    protected $table = 'coches';
    protected $fillable = ['marca_id', 'carroceria_id', 'pais_id', 'ano', 'nPlazas', 'cambio', 'estado', 'costeDia'];

    //Relación coche:marca(N:1)
    public function marca()
    {
        return $this->belongsTo(MarcaCoche::class);
    }

    //Relación coche:modelo(N:1)
    public function carroceria()
    {
        return $this->belongsTo(CarroceriaCoche::class);
    }

    //Relación coche:pais(N:1)
    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }

    //Relación coche:alquiler(1:N)
    public function alquileres()
    {
        return $this->hasMany(Alquiler::class);
    }
}
