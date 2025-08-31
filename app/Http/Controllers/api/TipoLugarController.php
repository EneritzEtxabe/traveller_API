<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TipoLugarResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\TipoLugar;

class TipoLugarController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tipoLugares",
     *     summary="Listar todos los tipos de lugares",
     *     tags={"TipoLugares"},
     *     security={{"sanctum":{}}},
     *     description="Obtiene la lista de todos los tipos de lugares disponibles. Solo accesible para usuarios registrados",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tipos de lugares obtenida correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="total", type="integer", example=3),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/TipoLugar")
     *             ),
     *         )
     *     )
     * )
     */
    public function index()
    {
        $tipoLugares = TipoLugar::get();
        $resultado = TipoLugarResource::collection($tipoLugares);
        return response()->json([
            'status' => 'success',
            'total' => $resultado->count(),
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/tipoLugares",
     *     summary="Crear un nuevo tipo de lugar",
     *     tags={"TipoLugares"},
     *     description="Solo accesible para administradores y superadministradores. Permite crear y añadir un nuevo tipo de lugar a la Base de Datos.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos para crear un tipo de lugar",
     *         @OA\JsonContent(ref="#/components/schemas/TipoLugarDB")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo de lugar creado correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/TipoLugar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permiso denegado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo los administradores pueden guardar un nuevo tipo de lugar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The nombre has already been taken.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo los administradores pueden guardar un nuevo tipo de lugar'], 403);
        };

        $data = $request->validate([
                'nombre' => 'required|string|unique:tipo_lugares,nombre|min:3',
            ],
            [
                'nombre.required' => 'Introduce un nombre de tipo de lugar',
                'nombre.string' => 'El nombre del tipo de lugar debe ser una cadena de texto.',
                'nombre.unique' =>'Ya existe ese tipo de lugar en nuestra BBDD',
                'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            ],
        );

        $tipoLugar = TipoLugar::create($data);
        
        $resultado = new TipoLugarResource($tipoLugar);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/tipoLugares/{id}",
     *     summary="Mostrar un tipo de lugar específico (por id)",
     *     tags={"TipoLugares"},
     *     security={{"sanctum":{}}},
     *     description="Obtiene los detalles de un tipo de lugar por su ID. Solo accesible para usuarios registrados",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del tipo de lugar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de lugar encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/TipoLugar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tipo de lugar no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tipo de lugar no encontrado")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        $tipoLugar = TipoLugar::find($id);
        if(!$tipoLugar){
            return response()->json(['message'=>'Tipo de lugar no encontrado'], 404);
        }
        $resultado = new TipoLugarResource($tipoLugar);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/tipoLugares/{id}",
     *     summary="Actualizar un tipo de lugar",
     *     tags={"TipoLugares"},
     *     description="Permite modificar los datos de un tipo de lugar. Solo accesible para administradores y superadministradores.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del tipo de lugar a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TipoLugarDB")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de lugar actualizado correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/TipoLugar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permiso denegado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo el administrador puede editar un tipo e lugar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tipo de lugar no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tipo de lugar no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ya existe un tipo de lugar con ese nombre en nuestra BBDD.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo el administrador puede editar un tipo e lugar'], 403);
        };
        $tipoLugar = TipoLugar::find($id);
        if(!$tipoLugar){
            return response()->json(['message'=>'Tipo de lugar no encontrado'], 404);
        }
        $data = $request->validate([
                'nombre' => 'sometimes|string|unique:tipo_lugares,nombre|min:3',
            ],
            [
                'nombre.string' => 'El nombre del tipo de lugar debe ser una cadena de texto.',
                'nombre.unique' =>'Ya existe ese tipo de lugar en nuestra BBDD',
                'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            ],
        );
        $tipoLugar->update($data);

        $resultado = new TipoLugarResource($tipoLugar);

        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/tipoLugares/{id}",
     *     summary="Eliminar un tipo de lugar",
     *     tags={"TipoLugares"},
     *     description="Permite eliminar un tipo de lugar. Solo accesible para administradores y superadministradores.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del tipo de lugar a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de lugar borrado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tipo de lugar borrado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permiso denegado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo el administrador puede borrar un tipo de lugar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tipo de lugar no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tipo de lugar no encontrado")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo el administrador puede borrar un tipo de lugar'], 403);
        };
        $tipoLugar = TipoLugar::find($id);
        if(!$tipoLugar){
            return response()->json(['message'=>'Tipo de lugar no encontrado'], 404);
        }
        $tipoLugar->delete();

        return response()->json(['message'=>'Tipo de lugar borrado correctamente'], 200);
    }
}
