<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\IdiomaResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\Idioma;

class IdiomaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/idiomas",
     *     summary="Listar todos los idiomas",
     *     tags={"Idiomas"},
     *     security={{"sanctum":{}}},
     *     description="Obtiene la lista de todos los idiomas disponibles. Solo accesible para usuarios registrados.",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de idiomas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status",type="string",example="success"),
     *             @OA\Property(property="total",type="integer",example=5),
     *             @OA\Property(property="data",type="array",@OA\Items(ref="#/components/schemas/Idioma"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $idiomas = Idioma::get();
        $resultado = IdiomaResource::collection($idiomas);
        return response()->json([
            'status' => 'success',
            'total' => $resultado->count(),
            'data' => $resultado
        ],200);
    }

    /**
     * @OA\Post(
     *     path="/api/idiomas",
     *     summary="Crear un nuevo idioma",
     *     description="Solo accesible para administradores y superadministradores. Permite crear y añadir un nuevo idioma a la Base de Datos.",
     *     tags={"Idiomas"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/IdiomaDB")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Idioma creado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Idioma")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permiso denegado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo los administradores pueden guardar un nuevo idioma")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo los administradores pueden guardar un nuevo idioma'], 403);
        };

        $data = $request->validate([
                'nombre' => 'required|string|unique:idiomas,nombre|min:3',
                'iso_639_1'=> 'nullable|unique:idiomas,iso_639_1|size:2',
            ],
            [
                'nombre.require' => 'Introduce un nombre de idioma',
                'nombre.string' => 'El nombre del idioma debe ser una cadena de texto.',
                'nombre.unique' => 'Ya existe un idioma con ese nombre en nuestra BBDD.',
                'nombre.min' => 'El nombre del idioma debe tener al menos 3 caracteres.',

                'iso_639_1.size'=>'El apartado iso_639_1 tiene que tener 2 caracteres',
                'iso_639_1.unique' => 'Ya existe un idioma con esa iso_639_1 en nuestra BBDD.'  
            ],
        );

        $idioma = Idioma::create($data);
        
        $resultado = new IdiomaResource($idioma);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/idiomas/{id}",
     *     summary="Mostrar un idioma específico (por id)",
     *     description="Obtiene los detalles de un idioma por su ID. Solo accesible para usuarios registrados",
     *     tags={"Idiomas"},
     *     security={{"sanctum":{}}},
     *     description="Devuelve un idioma específico por su ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del idioma a obtener",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Idioma encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Idioma")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Idioma no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Idioma no encontrado")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        $idioma = Idioma::find($id);
        if(!$idioma){
            return response()->json(['message'=>'Idioma no encontrado'], 404);
        }
        $resultado = new IdiomaResource($idioma);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ],200);
    }

    /**
     * @OA\Put(
     *     path="/api/idiomas/{id}",
     *     summary="Actualizar un idioma",
     *     tags={"Idiomas"},
     *     description="Permite modificar los datos de un idioma. Solo accesible para administradores y superadministradores.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del idioma a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(ref="#/components/schemas/IdiomaDB")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Idioma actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Idioma")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permiso denegado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo el administrador puede editar un idioma")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Idioma no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Idioma no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ya existe un idioma con ese nombre en nuestra BBDD.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo el administrador puede editar un idioma'], 403);
        };
        $idioma = Idioma::find($id);
        if(!$idioma){
            return response()->json(['message'=>'Idioma no encontrado'], 404);
        }
        $data = $request->validate([
                'nombre' => 'sometimes|string|unique:idiomas,nombre|min:3',
                'iso_639_1'=> 'nullable|unique:idiomas,iso_639_1|size:2',
            ],
            [
                'nombre.string' => 'El nombre del idioma debe ser una cadena de texto.',
                'nombre.unique' => 'Ya existe un idioma con ese nombre en nuestra BBDD.',
                'nombre.min' => 'El nombre del idioma debe tener al menos 3 caracteres.',

                'iso_639_1.size'=>'El apartado iso_639_1 tiene que tener 2 caracteres',
                'iso_639_1.unique' => 'Ya existe un idioma con esa iso_639_1 en nuestra BBDD.' 
            ],
        );
        

        $idioma->update($data);

        $resultado = new IdiomaResource($idioma);

        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/idiomas/{id}",
     *     summary="Eliminar un idioma",
     *     tags={"Idiomas"},
     *     description="Permite eliminar un idioma. Solo accesible para administradores y superadministradores.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del idioma a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Idioma borrado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Idioma borrado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permiso denegado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo el administrador puede borrar un idioma")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Idioma no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Idioma no encontrado")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo el administrador puede borrar un idioma'], 403);
        };
        $idioma = Idioma::find($id);
        if(!$idioma){
            return response()->json(['message'=>'Idioma no encontrado'], 404);
        }
        $idioma->delete();

        return response()->json(['message'=>'Idioma borrado correctamente'], 200);
    }
}
