<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CarroceriaCoche",
 *     title="Carrocería de Coche (Recurso API)",
 *     description="Recurso de carrocería de un coche (respuesta)",
 *     type="object",
 *     required={"id", "nombre"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="ID de la carrocería del coche"
 *     ),
 *     @OA\Property(
 *         property="nombre",
 *         type="string",
 *         example="SUV",
 *         description="Nombre de la carrocería del coche"
 *     )
 * )
 */
class CarroceriaCocheResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resultado =[
            'id' => $this->id,
            'nombre' => $this->nombre,
        ];
        return $resultado;
    }
}
