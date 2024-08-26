<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Trait\PurifyingHTML;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use PurifyingHTML;
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('comments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Comment::validateComment($request);
        $slug = Post::findOrFail($request->post_id)->slug;
        $sentized_content = PurifyingHTML::PurifyingHTML($request->content);
        Comment::create([
            'content'                   =>$sentized_content,
            'user_id'                   =>Auth::id(),
            'post_id'                   =>$request->post_id,
            'parent_id'                 =>$request->parent_id,
        ]);
        return redirect()->route('posts.show',$slug)
        ->with('success', 'comment added successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        return view('comments.edit',$comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        Comment::validateComment($request , $comment->id);
        $slug = Post::findOrFail($request->post_id)->slug;
        $sentized_content = PurifyingHTML::PurifyingHTML($request->content);
        Comment::where('id',$comment->id)->update([
            'content'                   =>$sentized_content,
            'user_id'                   =>Auth::id(),
            'post_id'                   =>$request->post_id,
            'parent_id'                 =>$request->parent_id,
        ]);
        return redirect()->route('posts.show',$slug)
        ->with('success', 'comment added successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->route('posts.index')
        ->with('danger', 'comment deleted successfully');
    }
}
