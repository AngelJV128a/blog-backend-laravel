<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="Post",
 *     type="object",
 *     title="Post",
 *     description="Esquema del modelo Post",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=5, description="ID del usuario que creó el post"),
 *     @OA\Property(property="title", type="string", example="Mi primer post", description="Título del post"),
 *     @OA\Property(property="content", type="string", example="Este es el contenido del post...", description="Contenido del post"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-23T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-23T11:30:00Z")
 * )
 */
class PostController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Obtener lista de posts con su cantidad de likes y comentarios",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de posts"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function index()
    {
        $posts = Post::withCount(['likes', 'comments'])->get();

        return response()->json($posts);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     summary="Mostrar un post específico",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del post",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post encontrado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post no encontrado"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function show($id)
    {
        $post = Post::find($id);

        return response()->json($post);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Crear un nuevo post",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","content"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="content", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post creado"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Datos inválidos"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $post = new Post();
        $post->title = $request->title;
        $post->content = $request->content;
        $post->user_id = auth()->user()->id;

        $post->save();

        return response()->json($post);
    }


    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     summary="Actualizar un post existente",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del post a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","content"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="content", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post actualizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post no encontrado"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        $post->title = $request->title;
        $post->content = $request->content;
        $post->save();

        return response()->json($post);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Eliminar un post",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del post a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post eliminado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post no encontrado"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        $post->delete();

        return response()->json($post);
    }
}
