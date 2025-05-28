<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Cantidad de elementos por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de posts paginados",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Post")),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="total", type="integer"),
     *             @OA\Property(property="per_page", type="integer")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */


    public function index()
    {
        Log::info('Listando posts para el usuario:');

        $posts = Post::with(['user'])
            ->withCount(['likes', 'comments'])
            ->paginate(10);

        Log::info('Posts listados');
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
        Log::info('Mostrando post con ID: ' . $id);
        $post = Post::with(['user'])->withCount(['likes'])->find($id);

        if(!$post) {
            Log::info('Post no encontrado');
            return response()->json(['message' => 'Post no encontrado'], 404);
        }
        Log::info('Post encontrado');
        return response()->json($post);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/user/{id_user}",
     *     summary="Obtener posts de un usuario",
     *     description="Retorna todos los posts creados por un usuario específico, incluyendo los datos del autor.",
     *     operationId="showPostsByUser",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id_user",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de posts con datos del usuario",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=10),
     *                 @OA\Property(property="title", type="string", example="Mi primer post"),
     *                 @OA\Property(property="content", type="string", example="Contenido del post..."),
     *                 @OA\Property(property="user_id", type="integer", example=3),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=3),
     *                     @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                     @OA\Property(property="email", type="string", example="juan@example.com")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
     */
    public function showPostsByUser($id_user)
    {
        Log::info('Mostrando posts de un usuario');
        $posts = Post::with(['user'])
            ->where('user_id', $id_user)
            ->paginate(10);
        return response()->json($posts);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/likes/{id_user}",
     *     operationId="getLikedPosts",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     summary="Obtener los posts que le gustaron a un usuario",
     *     description="Devuelve los posts que han sido marcados como 'me gusta' por un usuario específico, incluyendo el número de likes y comentarios por publicación.",
     *     @OA\Parameter(
     *         name="id_user",
     *         in="path",
     *         description="ID del usuario",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de posts con conteos de likes y comentarios",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Título del post"),
     *                 @OA\Property(property="content", type="string", example="Contenido del post"),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-08-15T14:12:00Z"),
     *                 @OA\Property(property="count_likes", type="integer", example=10),
     *                 @OA\Property(property="count_comentarios", type="integer", example=3)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado o sin publicaciones"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */
    public function showPostsByLikes($id_user)
    {
        Log::info('Mostrando posts con likes de un usuario');
        $posts = DB::table('posts')
            ->select(
                'posts.id',
                'posts.title',
                'posts.content',
                'posts.user_id',
                'users.name',
                'posts.updated_at',
                DB::raw('(SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) as count_likes'),
                DB::raw('(SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) as count_comentarios')
            )
            ->join('likes', 'posts.id', '=', 'likes.post_id')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->where('likes.user_id', $id_user)
            ->groupBy(
                'posts.id',
                'posts.title',
                'posts.content',
                'posts.user_id',
                'users.name',
                'posts.updated_at'
            )
            ->paginate(10); // 👈 Pagina los resultados

        return response()->json($posts);
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
        Log::info('Validando datos');
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000'
        ]);

        Log::info('Creando post');
        $post = new Post();
        $post->title = $request->title;
        $post->content = $request->content;
        $post->user_id = auth()->user()->id;

        $post->save();
        Log::info('Post creado');
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
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000',
        ]);

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
