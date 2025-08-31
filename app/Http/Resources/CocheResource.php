<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Coche",
 *     type="object",
 *     title="Coche (Rescurso API)",
 *     description="Representación de un coche con detalles de marca, carrocería, país y alquileres",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="ID único del coche"
 *     ),
 *     @OA\Property(
 *         property="marca",
 *         type="string",
 *         example="Toyota",
 *         description="Nombre de la marca del coche"
 *     ),
 *     @OA\Property(
 *         property="carroceria",
 *         type="string",
 *         example="Sedán",
 *         description="Tipo de carrocería"
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
 *         description="Número de plazas"
 *     ),
 *     @OA\Property(
 *         property="cambio",
 *         type="string",
 *         example="Manual",
 *         description="Tipo de cambio de marchas"
 *     ),
 *     @OA\Property(
 *         property="estado",
 *         type="string",
 *         example="Disponible",
 *         description="Estado del coche"
 *     ),
 *     @OA\Property(
 *         property="costeDia",
 *         type="number",
 *         format="float",
 *         example=35.5,
 *         description="Coste por día de alquiler"
 *     ),
 *     @OA\Property(
 *         property="pais",
 *         type="string",
 *         example="España",
 *         description="Nombre del país asociado"
 *     ),
 *     @OA\Property(
 *         property="alquileres",
 *         type="array",
 *         description="Fechas de alquiler",
 *         @OA\Items(
 *             type="string",
 *             example="2023-07-01 - 2023-07-10"
 *         )
 *     )
 * )
 */
class CocheResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->loadMissing(['marca','carroceria','pais','alquileres']);

        $resultado =[
            'id' => $this->id,
            'marca' => $this->marca->nombre,
            'carroceria'=>$this->carroceria->nombre,
            'ano'=>$this->ano,
            'nPlazas' => $this->nPlazas,
            'cambio'=>$this->cambio,
            'estado' => $this->estado,
            'costeDia' => $this->costeDia,
            'pais'=>$this->pais->nombre,
            'alquileres' => $this->alquileres->map(function($alquiler)
                {
                    return $alquiler->fecha_inicio . ' - ' . $alquiler->fecha_fin;
                }),
        ];

        return $resultado;
    }
}
