<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MarcaCocheResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\MarcaCoche;

class MarcaCocheController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/marcaCoches",
     *     operationId="getMarcasCoche",
     *     tags={"MarcasCoche"},
     *     summary="Listar todas las marcas de coche",
     *     description="Obtiene la lista de todas las marcas de coche disponibles. Solo accesible para usuarios registrados",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de marcas de coche obtenida correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="total", type="integer", example=3),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/MarcaCoche")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $marcasCoche = MarcaCoche::get();
        $resultado = MarcaCocheResource::collection($marcasCoche);
        return response()->json([
            'status' => 'success',
            'total' => $resultado->count(),
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/marcaCoches",
     *     operationId="storeMarcaCoche",
     *     tags={"MarcasCoche"},
     *     summary="Crear una nueva marca de coche",
     *     description="Solo accesible para administradores y superadministradores. Permite crear y añadir una nueva marca de coches a la Base de Datos.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MarcaCocheDB")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Marca de coche creada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/MarcaCoche")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado"
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
            return response()->json(['message'=>'Solo los administradores pueden guardar una nueva marca de coche'], 403);
        };

        $data = $request->validate([
                'nombre' => 'required|string|unique:marca_coches,nombre'
            ],
            [
                'nombre.reqired' => 'Introduce una marca de coches',
                'nombre.string'=>'La marca tiene que ser una cadena de texto',
                'nombre.unique' =>'Ya existe esa marca de coches en nuestra BBDD',
            ],
        );

        $marcaCoche = MarcaCoche::create($data);
        
        $resultado = new MarcaCocheResource($marcaCoche);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/marcaCoches/{id}",
     *     operationId="getMarcaCocheById",
     *     tags={"MarcasCoche"},
     *     security={{"sanctum":{}}},
     *     summary="Mostrar una marca de coche específica (por id)",
     *     description="Obtiene los detalles de la marca de un coche por su ID. Solo accesible para usuarios registrados",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la marca de coche",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Marca de coche encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/MarcaCoche")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Marca de coche no encontrada"
     *     )
     * )
     */
    public function show(string $id)
    {
        $marcaCoche = MarcaCoche::find($id);
        if(!$marcaCoche){
            return response()->json(['message'=>'Marca de coches no encontrada'], 404);
        }
        $resultado = new MarcaCocheResource($marcaCoche);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/marcaCoches/{id}",
     *     operationId="updateMarcaCoche",
     *     tags={"MarcasCoche"},
     *     summary="Actualizar una marca de coche",
     *     description="Permite modificar los datos de una marca de coches. Solo accesible para administradores y superadministradores.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la marca de coche a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MarcaCocheDB")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Marca de coche actualizada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/MarcaCoche")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Marca de coche no encontrada"
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
            return response()->json(['message'=>'Solo el administrador puede editar una marca de coches'], 403);
        };
        $marcaCoche = MarcaCoche::find($id);
        if(!$marcaCoche){
            return response()->json(['message'=>'Marca de coches no encontrada'], 404);
        }
        $data = $request->validate([
                'nombre' => 'required|string|unique:marca_coches,nombre'
            ],
            [
                'nombre.string'=>'La marca tiene que ser una cadena de texto',
                'nombre.unique' =>'Ya existe esa marca de coches en nuestra BBDD',
            ],
        );
        

        $marcaCoche->update($data);

        $resultado = new MarcaCocheResource($marcaCoche);

        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/marcaCoches/{id}",
     *     operationId="deleteMarcaCoche",
     *     tags={"MarcasCoche"},
     *     summary="Eliminar una marca de coche",
     *     description="Permite eliminar una marca de coches. Solo accesible para administradores y superadministradores.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la marca de coche a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Marca de coche borrada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Marca de coches borrada correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo el administrador puede borrar una marca de coches")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Marca de coche no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Marca de coches no encontrada")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo el administrador puede borrar una marca de coches'], 403);
        };
        $marcaCoche = MarcaCoche::find($id);
        if(!$marcaCoche){
            return response()->json(['message'=>'Marca de coches no encontrada'], 404);
        }
        $marcaCoche->delete();

        return response()->json(['message'=>'Marca de coches borrada correctamente'], 200);
    }
}
