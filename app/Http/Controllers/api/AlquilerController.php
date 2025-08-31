<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlquilerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Models\Alquiler;
use App\Models\Coche;

class AlquilerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/alquileres",
     *     summary="Listar todos los alquileres",
     *     description="Obtiene la lista de todos los alquileres. Solo accesible para administradores y superadministradores",
     *     tags={"Alquileres"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de alquileres obtenida correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="total", type="integer", example=10),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Alquiler")
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado. Solo los administradores/superadministradores pueden ver todos los alquileres.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Solo los administradores/superadministradores pueden ver todos los alquileres")
     *         )
     *     )
     * )
     */
    public function index()
    {
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin')){
            return response()->json(['message'=>'Solo el administrador puede ver todos los alquileres'], 403);
        };

        $alquileres = Alquiler::with('cliente')->get();
        $resultado = AlquilerResource::collection($alquileres);
        return response()->json([
            'status' => 'success',
            'total' => $resultado->count(),
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/alquileres",
     *     summary="Crear un nuevo alquiler",
     *     description="Solo accesible para usuarios registrados. Los clientes solo podrán crear el alquiler a su nombre, los admin/superadmin en cambio podrán crear un alquiler tanto a su nombre como a nombre de otro cliente",
     *     tags={"Alquileres"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos para crear un alquiler",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"coche_id", "fecha_inicio", "fecha_fin"},
     *             @OA\Property(property="coche_id", type="integer", example=1, description="ID del coche a alquilar"),
     *             @OA\Property(property="cliente_id", type="integer", example=2, description="ID del cliente. Obligatorio para admin y superadmin. Omitir para usuarios normales"),
     *             @OA\Property(property="fecha_inicio", type="string", format="date", example="2025-09-01", description="Fecha de inicio del alquiler"),
     *             @OA\Property(property="fecha_fin", type="string", format="date", example="2025-09-05", description="Fecha de fin del alquiler, posterior a fecha_inicio")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Alquiler creado correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Alquiler")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No tienes permisos para alquilar en nombre de otro cliente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No tienes permisos para alquilar en nombre de otro cliente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos o cliente_id obligatorio para admin/superadmin",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El ID del cliente es obligatorio para los administradores y superadministradores")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="El coche no está disponible para alquilar",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El coche no está disponible para alquilar.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data=$request->all();

        if (Gate::denies('es-admin') && Gate::denies('es-superadmin')) {
            if(isset($data['cliente_id']) && $data['cliente_id']!==Auth::user()->id){
                return response()->json(['message' => 'No tienes permisos para alquilar en nombre de otro cliente.'], 403);
            }
            $data['cliente_id'] = Auth::user()->id;
        }else {
            if (!isset($data['cliente_id'])) {
                return response()->json(['message' => 'El ID del cliente es obligatorio para los administradores y superadministradores'], 422);
            }
        }

        $validatedData = validator($data,[
            'coche_id'=>'required|exists:coches,id',
            'cliente_id' => 'required|exists:users,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin'=>'required|date|after:fecha_inicio',
        ],
        [
            'coche_id.required' => 'Introduce el id del coche que se quiere alquilar.',
            'coche_id.exists' => 'El coche seleccionado no existe en nuestra BBDD.',

            'cliente_id.required' => 'El ID del cliente es obligatorio para los administradores y superadministradores',
            'cliente_id.exists' => 'El cliente seleccionado no existe en nuestra BBDD.',

            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio debe tener un formato válido.',

            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin debe tener un formato válido.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ])->validate();

        $coche = Coche::find($validatedData['coche_id']);

        if ($coche->estado !== 'disponible') {
            return response()->json(['message' => 'El coche no está disponible para alquilar.'], 400);
        }

        $conflicto = Alquiler::where('coche_id', $validatedData['coche_id'])
            ->where(function($query) use ($validatedData) {
                $query->whereBetween('fecha_inicio', [$validatedData['fecha_inicio'], $validatedData['fecha_fin']])
                      ->orWhereBetween('fecha_fin', [$validatedData['fecha_inicio'], $validatedData['fecha_fin']])
                      ->orWhere(function($query) use ($validatedData) {
                            $query->where('fecha_inicio', '<=', $validatedData['fecha_inicio'])
                                  ->where('fecha_fin','>=', $validatedData['fecha_fin']);
                      });
            })
            ->exists();

        if ($conflicto) {
            return response()->json(['message' => 'El coche no está disponible en las fechas seleccionadas.'], 400);
        }

        $inicio = Carbon::parse($validatedData['fecha_inicio']);
        $fin = Carbon::parse($validatedData['fecha_fin']);
        $diasAlquiler = $inicio->diffInDays($fin) + 1;
        $costeTotal = $diasAlquiler * $coche->costeDia;
        $validatedData['coste'] = round($costeTotal,2);

        $alquiler = Alquiler::create($validatedData);

        $resultado = new AlquilerResource($alquiler);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/alquileres/{id}",
     *     summary="Mostrar un alquiler específico (por id)",
     *     description="Obtiene los detalles de un alquiler por su ID. Solo accesible para administradores, superadministradores o el cliente creador del alquiler.",
     *     tags={"Alquileres"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del alquiler",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del alquiler",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Alquiler")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo el administrador o el propio cliente puede ver un alquiler")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Alquiler no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Alquiler no encontrado")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        $alquiler = Alquiler::with('cliente')->find($id);
        if(!$alquiler){
            return response()->json(['message'=>'Alquiler no encontrado'], 404);
        }

        if(Gate::denies('es-admin') && Gate::denies('es-superadmin') && Gate::denies('es-creador-alquiler',$alquiler)){
            return response()->json(['message'=>'Solo el administrador o el propio cliente puede ver un alquiler'], 403);
        };
        $resultado = new AlquilerResource($alquiler);
        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/alquileres/{id}",
     *     summary="Actualizar un alquiler",
     *     description="Permite modificar los datos de un alquiler (menos el id del cliente que ha hecho la reserva, ni el coste total del alquiler). Solo accesible para administradores, superadministradores o el cliente creador del alquiler.",
     *     tags={"Alquileres"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del alquiler a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="coche_id", type="integer", example=5),
     *             @OA\Property(property="fecha_inicio", type="string", format="date", example="2025-09-01"),
     *             @OA\Property(property="fecha_fin", type="string", format="date", example="2025-09-05")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Alquiler actualizado correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Alquiler")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado. Solo el administrador o el cliente creador puede editar este alquiler.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo el administrador o el propio cliente puede editar un alquiler")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Alquiler no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Alquiler no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Intento de modificar campos restringidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se permite modificar el ID del cliente que alquila el coche ni el ID del propio alquiler")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="El coche no está disponible para alquilar",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El coche no está disponible para alquilar.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $alquiler = Alquiler::find($id);
        if(!$alquiler){
            return response()->json(['message'=>'Alquiler no encontrado'], 404);
        }
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin') && Gate::denies('es-creador-alquiler',$alquiler)){
            return response()->json(['message'=>'Solo el administrador o el propio cliente puede editar un alquiler'], 403);
        };

        if ($request->has('id') && $request->input('id') != $id) {
            return response()->json(['message' => 'No se permite modificar el ID del alquiler'], 422);
        }

        if ($request->has('cliente_id') && $request->input('cliente_id') != $alquiler->cliente_id) {
            return response()->json(['message' => 'No se permite modificar el ID del cliente que alquila el coche'], 422);
        }

        $data = $request->validate([
                'coche_id'=>'sometimes|exists:coches,id',
                'cliente_id' => 'sometimes|exists:users,id',
                'fecha_inicio' => 'sometimes|date',
                'fecha_fin'=>'sometimes|date|after:fecha_inicio',
            ],
            [
                'coche_id.exists' => 'El coche seleccionado no existe en nuestra BBDD.',

                'cliente_id.exists' => 'El cliente seleccionado no existe en nuestra BBDD.',

                'fecha_inicio.date' => 'La fecha de inicio debe tener un formato válido.',

                'fecha_fin.date' => 'La fecha de fin debe tener un formato válido.',
                'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            ],
        );

        if (isset($data['coche_id'], $data['fecha_inicio'], $data['fecha_fin'])){
            $coche = Coche::find($data['coche_id']);

            if ($coche->estado !== 'disponible') {
                return response()->json(['message' => 'El coche no está disponible para alquilar.'], 400);
            }

            $conflicto = Alquiler::where('coche_id', $data['coche_id'])
                ->where(function($query) use ($data) {
                    $query->whereBetween('fecha_inicio', [$data['fecha_inicio'], $data['fecha_fin']])
                        ->orWhereBetween('fecha_fin', [$data['fecha_inicio'], $data['fecha_fin']])
                        ->orWhere(function($query) use ($data) {
                                $query->where('fecha_inicio', '<=', $data['fecha_inicio'])
                                    ->where('fecha_fin','>=', $data['fecha_fin']);
                        });
                })
                ->exists();

            if ($conflicto) {
                return response()->json(['message' => 'El coche no está disponible en las fechas seleccionadas.'], 400);
            }

            $inicio = Carbon::parse($data['fecha_inicio']);
            $fin = Carbon::parse($data['fecha_fin']);
            $diasAlquiler = $inicio->diffInDays($fin) + 1;
            $costeTotal = $diasAlquiler * $coche->costeDia;
            $data['coste'] = round($costeTotal,2);
        }

        $alquiler->update($data);

        $resultado = new AlquilerResource($alquiler);

        return response()->json([
            'status' => 'success',
            'data' => $resultado
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/alquileres/{id}",
     *     summary="Eliminar un alquiler",
     *     description="Permite eliminar un alquiler. Solo accesible para administradores, superadministradores o el cliente creador del alquiler.",
     *     tags={"Alquileres"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del alquiler a eliminar",
     *         required=true,
     *         @OA\Schema(type="string", example="1")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Alquiler borrado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Alquiler borrado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo el administrador o el propio cliente puede eliminar un alquiler")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Alquiler no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Alquiler no encontrado")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        
        $alquiler = Alquiler::find($id);
        if(!$alquiler){
            return response()->json(['message'=>'Alquiler no encontrado'], 404);
        }
        if(Gate::denies('es-admin') && Gate::denies('es-superadmin') && Gate::denies('es-creador-alquiler',$alquiler)){
            return response()->json(['message'=>'Solo el administrador o el propio cliente puede eliminar un alquiler'], 403);
        };
       
        $alquiler->delete();

        return response()->json(['message'=>'Alquiler borrado correctamente'], 200);
    }
}
