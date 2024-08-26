<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Trait\PurifyingHTML;
use Illuminate\Support\Facades\Auth;
    /**
     * @OA\Schema(
     *     schema="Comment",
     *     required={"content", "post_id", "user_id"},
     *     @OA\Property(
     *         property="content",
     *         type="string",
     *         description="The content of the comment"
     *     ),
     *     @OA\Property(
     *         property="post_id",
     *         type="integer",
     *         description="The ID of the post the comment belongs to"
     *     ),
     *     @OA\Property(
     *         property="user_id",
     *         type="integer",
     *         description="The ID of the user who created the comment"
     *     ),
     *     @OA\Property(
     *         property="parent_id",
     *         type="integer",
     *         description="The ID of the parent comment if this is a reply",
     *         nullable=true
     *     ),
     * )
     */
class CommentController extends Controller
{
    use PurifyingHTML;

        /**
     * @OA\Get(
     *     path="/api/comments/create",
     *     summary="Show the form for creating a new comment",
     *     @OA\Response(
     *         response=200,
     *         description="Form to create a new comment"
     *     )
     * )
     */
    public function create()
    {
        
    }

    /**
     * @OA\Post(
     *     path="/api/comments",
     *     summary="Store a newly created comment in storage",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comment created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/comments/{id}/edit",
     *     summary="Show the form for editing the specified comment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form to edit a comment"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found"
     *     )
     * )
     */
    public function edit(Comment $comment)
    {
        return view('comments.edit',$comment);
    }

/**
     * @OA\Put(
     *     path="/api/comments/{id}",
     *     summary="Update the specified comment in storage",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/comments/{id}",
     *     summary="Remove the specified comment from storage",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found"
     *     )
     * )
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->route('posts.index')
        ->with('danger', 'comment deleted successfully');
    }
}
