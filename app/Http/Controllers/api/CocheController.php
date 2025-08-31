<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CocheResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\Coche;

class CocheController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/coches",
     *     summary="Listar todos los coches",
     *     description="Obtiene la lista de todos los coches disponibles. Solo accesible para usuarios registrados.",
     *     tags={"Coches"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de coches con datos completos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="total", type="integer", example=5),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Coche")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $coches = Coche::with('carroceria', 'marca','pais','alquileres')->get();
        $resultado = CocheResource::collection($coches);
        return response()->json([
            'status' => 'success',
            'total' => $resultado->count(),
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/coches",
     *     summary="Crear un nuevo coche",
     *     description="Solo accesible para administradores y superadministradores. Permite crear y añadir un nuevo coche a la Base de Datos.",
     *     tags={"Coches"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CocheDB")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Coche creado correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Coche")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permiso denegado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function store(Request $request)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo los administradores pueden guardar un nuevo coche'], 403);
        };

        $data = $request->validate([
            'marca_id' => 'required|exists:marca_coches,id',
            'carroceria_id' => 'required|exists:carroceria_coches,id',
            'ano' => 'nullable|integer|digits:4',
            'nPlazas' => 'required|integer|in:2,4,5,7',
            'cambio' => 'nullable|in:manual,automatico',
            'estado' => 'nullable|in:disponible,mantenimiento',
            'costeDia' => 'required|numeric|between:0,99.99',
            'pais_id' => 'required|exists:paises,id',
        ],
        [
            'marca_id.required' => 'Introduce el id de la marca del coche.',
            'marca_id.exists' => 'El id de la marca seleccionada no existe en nuestra BBDD.',

            'carroceria_id.required' => 'Introduce el id de la carrocería del coche.',
            'carroceria_id.exists' => 'La carrocería seleccionada no existe en nuestra BBDD.',

            'ano.integer' => 'El año debe ser un número entero.',
            'ano.digits' => 'El año debe ser un número entero de 4 dígitos.',

            'nPlazas.required' => 'El número de plazas es obligatorio.',
            'nPlazas.integer' => 'El número de plazas debe ser un número entero.',
            'nPlazas.in' => 'El número de plazas debe ser 2, 4, 5 o 7.',

            'cambio.in' => 'El tipo de cambio debe ser manual o automático.',

            'estado.in' => 'El estado debe ser disponible o mantenimiento.',

            'costeDia.required' => 'Indica el coste por día de alquilar este coche.',
            'costeDia.numeric' => 'El coste por día debe ser un valor numérico.',
            'costeDia.between' => 'El coste por día debe estar entre 0 y 99.99 €.',

            'pais_id.required' => 'El país es obligatorio.',
            'pais_id.exists' => 'El id del país seleccionado no existe en nuestra BBDD.',
        ],
        );

        $coche = Coche::create($data);

        $resultado = new CocheResource($coche);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/coches/{id}",
     *     summary="Mostrar un coche específico (por id)",
     *     description="Obtiene los detalles de un coche por su ID. Solo accesible para usuarios registrados",
     *     tags={"Coches"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del coche a mostrar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del coche",
     *         @OA\JsonContent(ref="#/components/schemas/Coche")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Coche no encontrado"
     *     )
     * )
     */
    public function show(string $id)
    {
        $coche = Coche::with('carroceria', 'marca','pais','alquileres')->find($id);
        if(!$coche){
            return response()->json(['message'=>'Coche no encontrado'], 404);
        }
        $resultado = new CocheResource($coche);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/coches/{id}",
     *     summary="Actualizar un coche",
     *     description="Permite modificar los datos de un coche. Solo accesible para administradores y superadministradores.",
     *     tags={"Coches"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del coche a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CocheDB")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Coche actualizado correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Coche")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Solo el administrador puede editar un coche"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Coche no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo el administrador puede editar un coche'], 403);
        };
        $coche = Coche::find($id);
        if(!$coche){
            return response()->json(['message'=>'Coche no encontrado'], 404);
        }

        $data = $request->validate([
                'marca_id' => 'sometimes|exists:marca_coches,id',
                'carroceria_id' => 'sometimes|exists:carroceria_coches,id',
                'ano' => 'nullable|integer|digits:4',
                'nPlazas' => 'sometimes|integer|in:2,4,5,7',
                'cambio' => 'nullable|in:manual,automatico',
                'estado' => 'nullable|in:disponible,mantenimiento',
                'costeDia' => 'sometimes|numeric|between:0,99.99',
                'pais_id' => 'sometimes|exists:paises,id',
            ],
            [
                'marca_id.exists' => 'El id de la marca seleccionada no existe en nuestra BBDD.',

                'carroceria_id.exists' => 'La carrocería seleccionada no existe en nuestra BBDD.',

                'ano.integer' => 'El año debe ser un número entero.',
                'ano.digits' => 'El año debe ser un número entero de 4 dígitos.',

                'nPlazas.integer' => 'El número de plazas debe ser un número entero.',
                'nPlazas.in' => 'El número de plazas debe ser 2, 4, 5 o 7.',

                'cambio.in' => 'El tipo de cambio debe ser manual o automático.',

                'estado.in' => 'El estado debe ser disponible o mantenimiento.',

                'costeDia.numeric' => 'El coste por día debe ser un valor numérico.',
                'costeDia.between' => 'El coste por día debe estar entre 0 y 99.99 €.',

                'pais_id.exists' => 'El id del país seleccionado no existe en nuestra BBDD.',
            ],
        );

        $coche->update($data);

        $resultado = new CocheResource($coche);

        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/coches/{id}",
     *     summary="Borrar un coche",
     *     description="Permite eliminar un coche. Solo accesible para administradores y superadministradores.",
     *     tags={"Coches"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del coche a borrar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Coche borrado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Coche borrado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Solo el administrador puede borrar un coche",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo el administrador puede borrar un coche")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Coche no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Coche no encontrado")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo el administrador puede borrar un coche'], 403);
        };
        $coche = Coche::find($id);
        if(!$coche){
            return response()->json(['message'=>'Coche no encontrado'], 404);
        }
        $coche->delete();

        return response()->json(['message'=>'Coche borrado correctamente'], 200);
    }
}
