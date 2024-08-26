<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Trait\NamingUploadedImages;
use App\Trait\PurifyingHTML;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @OA\Info(
 *     title="brandify",
 *     version="1.0.0",
 *     description="This is the API documentation for My API."
 * )
 */

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="List all posts",
     *     @OA\Response(
     *         response=200,
     *         description="A list of posts"
     *     )
     * )
     */
    use PurifyingHTML;
    public function index(Request $request)
    {
        $posts = Post::with(['author','comments.children'])->get();
        if($request->expectsJson()){
            return response()->json($posts);
        }
        return view('dashboard',compact('posts'));
    }

    /**
     * @OA\Get(
     *     path="/api/posts/create",
     *     summary="Show the form for creating a new post",
     *     @OA\Response(
     *         response=200,
     *         description="Form to create a new post"
     *     )
     * )
     */
    public function create(Request $request)
    {
        if($request->expectsJson()){
            return response()->json();
        }
        return view('posts.create');
    }

/**
 * @OA\Post(
 *     path="/posts",
 *     summary="Create a new post",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Schema(
 *                 ref="#/components/schemas/Post"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Created",
 *         @OA\JsonContent(
 *             @OA\Schema(
 *                 ref="#/components/schemas/Post"
 *             )
 *         )
 *     )
 * )
 */   
    public function store(Request $request)
    {
        
        Post::ValidatePost($request);
        if($request->hasFile('featured_image')){
            $uploaded_img = $request->file('featured_image');
            $uploaded_path = $uploaded_img->storeAs('posts',
            NamingUploadedImages::AccordingToModel($request->name ?? "").".".$uploaded_img->getClientOriginalExtension());
        }
        $sentized_content = PurifyingHTML::PurifyingHTML($request->content);
        $post = Post::create([
            'title'                     =>$request->title,
            'slug'                      =>Str::slug($request->title),
            'excerpt'                   =>$request->excerpt ?? Str::excerpt($sentized_content),
            'content'                   =>$sentized_content,
            'meta_title'                =>$request->meta_title,
            'meta_description'          =>$request->meta_description,
            'meta_keywords'             =>$request->meta_keywords,
            'featured_image'            =>$uploaded_path ?? null ,
            'user_id'                   =>Auth::id(),
        ]);

        if($request->expectsJson()){
            return response()->json([
                'status'                  =>'success',
                'message'                 =>"$post->title post created successfully",
                'data'                      =>[
                    'title'                 =>$post->title,
                    'link'                  =>route('posts.show',$post->slug)
                ],
            ]);
        }
        return redirect()->route('posts.index')
        ->with('success',"$post->title posted successfully");
    }

        /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     summary="Display the specified post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Display a single post"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     )
     * )
     */
    public function show(Post $post , Request $request)
    {

        $post = Post::with(['comments.children','author'])->get();
        if($request->expectsJson()){
            return response()->json($post);
        }
        return $post;
    }

    /**
 * @OA\Get(
 *     path="/posts/{id}/edit",
 *     summary="Edit an existing post",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the post to edit"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful"
 *     )
 * )
 */
    public function edit($id , Request $request)
    {
        $post = Post::findOrFail($id);
        if($request->expectsJson()){
            return response()->json($post);
        }
        return view('posts.edit',compact('post'));
    }

    /**
 * @OA\Put(
 *     path="/posts/{id}",
 *     summary="Update an existing post",
 *     @OA\Parameter(
 *         in="path",
 *         name="id",
 *         required=true,
 *         description="ID of the post to update"
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Schema(
 *                 ref="#/components/schemas/Post"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Updated",
 *         @OA\JsonContent(
 *             @OA\Schema(
 *                 ref="#/components/schemas/Post"
 *             )
 *         )
 *     )
 * )
 */
    public function update(Request $request, Post $post)
    {
        Post::ValidatePost($request);

        $old_path = $post->featured_image;
        if($request->hasFile('featured_image')){
            $uploaded_img = $request->file('featured_image');
            $uploaded_path = $uploaded_img->storeAs('posts',
            NamingUploadedImages::AccordingToModel($request->name ?? "").".".$uploaded_img->getClientOriginalExtension());
        }

        $sentized_content = PurifyingHTML::PurifyingHTML($request->content);

        if($request->has('remove_img') && $request->remove_img == true){
            $uploaded_path = null;
        }

        $post = Post::where('id',$post->id)->update([
            'title'                     =>$request->title,
            'slug'                      =>Str::slug($request->title),
            'excerpt'                   =>$request->excerpt ?? Str::excerpt($sentized_content),
            'content'                   =>$sentized_content,
            'meta_title'                =>$request->meta_title,
            'meta_description'          =>$request->meta_description,
            'meta_keywords'             =>$request->meta_keywords,
            'featured_image'            =>$uploaded_path ?? $old_path ,
            'user_id'                   =>Auth::id(),
        ]);

        if($request->hasFile('featured_image') && $old_path){
            Storage::delete($old_path);
        }

        if($request->expectsJson()){
            return response()->json([
                'status'                  =>'success',
                'message'                 =>"$post->title post updated successfully",
                'data'                      =>[
                    'title'                 =>$post->title,
                    'link'                  =>route('posts.show',$post->slug)
                ],
            ]);
        }
        return redirect()->route('posts.show',$post->slug)
        ->with('success',"$post->title updated successfully");
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Remove the specified post from storage",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     )
     * )
     */
    public function destroy(Post $post , Request $request)
    {
        $img = $post->featured_image;

        $post->delete();

        if($img){
            Storage::delete($img);
        }

        if($request->expectsJson()){
            return response()->json([
                'status'            =>'success',
                'messege'           =>"$post->title removed successfully"
            ]);
        }

        return redirect()->route('posts.index')
        ->with('danger',"$post->title has been deleted successfully");
    }
}
