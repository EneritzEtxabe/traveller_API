<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="AlquilerDB",
 *   type="object",
 *   title="Alquiler (BBDD)",
 *   required={"fecha_inicio", "fecha_fin", "coste", "coche_id", "cliente_id"},
 *   @OA\Property(property="fecha_inicio", type="string", format="date", description="*Fecha de inicio del alquiler (YYYY-MM-DD)*", example="2025-08-21"),
 *   @OA\Property(property="fecha_fin", type="string", format="date", description="*Fecha de fin del alquiler (YYYY-MM-DD)*", example="2025-08-28"),
 *   @OA\Property(property="coche_id", type="integer", description="*ID del coche asociado al alquiler*", example=42),
 *   @OA\Property(property="cliente_id", type="integer", description="*ID del cliente que realizó el alquiler*", example=7),
 *   @OA\Property(property="coste", type="number", format="float", example=250.50),
 * )
 */
class Alquiler extends Model
{
    use HasFactory;
    
    protected $table = 'alquileres';
    protected $fillable = ['fecha_inicio','fecha_fin','coche_id','cliente_id', 'coste'];

    //Relación alquiler:coche(N:1)
    public function coche()
    {
        return $this->belongsTo(Coche::class);
    }

    // Relación alquiler:cliente(N:1)
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }
}
