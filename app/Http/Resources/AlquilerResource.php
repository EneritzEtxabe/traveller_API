<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Alquiler",
 *     type="object",
 *     title="Alquiler (Recurso API)",
 *     description="Datos detallados de un alquiler",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(
 *         property="coche",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=3),
 *         @OA\Property(property="marca", type="string", example="Toyota"),
 *         @OA\Property(property="carroceria", type="string", example="SUV"),
 *         @OA\Property(property="ano", type="integer", example=2022),
 *         @OA\Property(property="nPlazas", type="integer", example=5),
 *         @OA\Property(property="cambio", type="string", example="Automático"),
 *         @OA\Property(property="estado", type="string", example="Disponible"),
 *         @OA\Property(property="costeDia", type="number", format="float", example=45.99),
 *         @OA\Property(property="pais", type="string", example="España")
 *     ),
 *     @OA\Property(
 *         property="cliente", 
 *         type="string",
 *         @OA\Property(property="id", type="integer", example=3),
 *         @OA\Property(property="nombre", type="string", example="José"),
 *     ),
 *     @OA\Property(property="fecha_inicio", type="string", format="date", example="2025-08-30"),
 *     @OA\Property(property="fecha_fin", type="string", format="date", example="2025-09-05"),
 *     @OA\Property(property="coste", type="number", format="float", example=229.95)
 * )
 */
class AlquilerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->loadMissing(['coche.marca','coche.carroceria','coche.pais','cliente']);

        $resultado =[
            'id' => $this->id,
            'coche'=>[
                'id' => $this->coche->id,
                'marca' => $this->coche->marca->nombre,
                'carroceria'=>$this->coche->carroceria->nombre,
                'ano'=>$this->coche->ano,
                'nPlazas' => $this->coche->nPlazas,
                'cambio'=>$this->coche->cambio,
                'estado' => $this->coche->estado,
                'costeDia' => $this->coche->costeDia,
                'pais'=>$this->coche->pais->nombre,
            ],
            'cliente' =>[
                'id'=>$this->cliente->id,
                'nombre'=>$this->cliente->nombre,
            ],
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin'=>$this->fecha_fin,
            'coste'=>$this->coste,
        ];
        return $resultado;
    }
}
