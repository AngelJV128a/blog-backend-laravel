<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Schema(
 *     schema="Comment",
 *     type="object",
 *     title="Comment",
 *     description="Esquema del modelo Comment",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="post_id", type="integer", example=10),
 *     @OA\Property(property="user_id", type="integer", example=5),
 *     @OA\Property(property="content", type="string", example="Excelente post, gracias por compartir."),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-01T14:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-05-01T15:00:00Z")
 * )
 */


class CommentController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @OA\Get(
     *     path="/api/comments",
     *     summary="Obtener comentarios de un post",
     *     tags={"Comentarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post_id",
     *         in="query",
     *         required=true,
     *         description="ID del post",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de comentarios",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Comment"))
     *     ),
     *     @OA\Response(response=401, description="No autorizado")
     * )
     */
    public function index(Request $request)
    {
        Log::info('Listando comentarios');
        $comments = Comment::where('post_id', $request->post_id)->get();
        Log::info('Comentarios listados');

        return response()->json($comments);
    }

    /**
     * @OA\Post(
     *     path="/api/comments",
     *     summary="Crear un nuevo comentario",
     *     tags={"Comentarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"post_id", "user_id", "content"},
     *             @OA\Property(property="post_id", type="integer", example=10),
     *             @OA\Property(property="user_id", type="integer", example=5),
     *             @OA\Property(property="content", type="string", example="Gran aporte, muchas gracias.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comentario creado",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(response=401, description="No autorizado")
     * )
     */
    public function store(Request $request)
    {
        Log::info('Validando datos');
        $request->validate([
            'post_id' => 'required|integer',
            'user_id' => 'required|integer',
            'content' => 'required|string|max:10000'
        ]);

        Log::info('Creando comentario');
        $comment = new Comment();
        $comment->post_id = $request->post_id;
        $comment->user_id = $request->user_id;
        $comment->content = $request->content;
        $comment->save();
        Log::info('Comentario creado');

        return response()->json($comment);
    }

    /**
     * @OA\Put(
     *     path="/api/comments/{id}",
     *     summary="Actualizar un comentario",
     *     tags={"Comentarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del comentario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(property="content", type="string", example="Comentario editado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comentario actualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(response=401, description="No autorizado")
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:10000'
        ]);
        Log::info('Actualizando comentario');
        $comment = Comment::find($id);
        $comment->content = $request->content;
        $comment->save();
        Log::info('Comentario actualizado');

        return response()->json($comment);
    }

    /**
     * @OA\Delete(
     *     path="/api/comments/{id}",
     *     summary="Eliminar un comentario",
     *     tags={"Comentarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del comentario a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comentario eliminado",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(response=401, description="No autorizado")
     * )
     */
    public function destroy($id)
    {
        Log::info('Eliminando comentario');
        $comment = Comment::find($id);
        $comment->delete();
        Log::info('Comentario eliminado');

        return response()->json($comment);
    }
}
