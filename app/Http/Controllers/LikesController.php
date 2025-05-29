<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Schema(
 *     schema="Like",
 *     type="object",
 *     title="Like",
 *     description="Esquema de un like",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="post_id", type="integer", example=3),
 *     @OA\Property(property="user_id", type="integer", example=2),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-05-01T12:00:00Z")
 * )
 */
class LikesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }


    /**
     * @OA\Post(
     *     path="/api/likes",
     *     summary="Dar like a un post",
     *     tags={"Likes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"post_id", "user_id"},
     *             @OA\Property(property="post_id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Like registrado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Like")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function giveLike(Request $request)
    {
        Log::info('Dar like a un post');
        $post = Post::find($request->post_id);
        $user = User::find($request->user_id);

        Log::info('Post encontrado');
        $like = new Like();
        $like->post_id = $post->id;
        $like->user_id = $user->id;
        $like->save();
        Log::info('Like registrado');

        return response()->json($like);
    }

    /**
     * @OA\Delete(
     *     path="/api/likes",
     *     summary="Eliminar like de un post",
     *     tags={"Likes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"post_id", "user_id"},
     *             @OA\Property(property="post_id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Like eliminado correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Like")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function deleteLike(Request $request)
    {
        Log::info('Eliminar like de un post');
        $like = Like::where('post_id', $request->post_id)->where('user_id', $request->user_id)->first();
        $like->delete();
        Log::info('Like eliminado');

        return response()->json($like);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{post_id}/likes",
     *     summary="Mostrar número de likes de un post",
     *     tags={"Likes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID del post"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cantidad de likes del post",
     *         @OA\JsonContent(
     *             @OA\Property(property="post_id", type="integer", example=1),
     *             @OA\Property(property="likes", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function showLikes(Request $request, $post_id)
    {
        Log::info('Mostrando likes de un post');
        $likesCount = Like::where('post_id', $post_id)->count();

        Log::info('Likes obtenidos');
        return response()->json([
            'post_id' => $post_id,
            'likes' => $likesCount
        ]);
    }
}
