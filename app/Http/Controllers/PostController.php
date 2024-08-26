<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Trait\NamingUploadedImages;
use App\Trait\PurifyingHTML;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use PurifyingHTML;
    public function index()
    {
        $posts = Post::with(['author','comments.children'])->get();
        return view('dashboard',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
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

        return redirect()->route('posts.index')
        ->with('success',"$post->title posted successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {

        $post = Post::with(['comments.children','author'])->get();
        return $post;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('posts.edit',compact('post'));
    }

    /**
     * Update the specified resource in storage.
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

        return redirect()->route('posts.show',$post->slug)
        ->with('success',"$post->title updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $img = $post->featured_image;

        $post->delete();

        if($img){
            Storage::delete($img);
        }

        return redirect()->route('posts.index')
        ->with('danger',"$post->title has been deleted successfully");
    }
}
