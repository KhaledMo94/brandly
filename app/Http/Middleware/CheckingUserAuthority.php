<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CheckingUserAuthority
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next ,String $type ): Response
    {
        if (app()->environment('local') && $this->isSwaggerRequest($request)) {
            return $next($request);
        }
        if($type == 'post'){
            $post_id = $request->route('post');
            $post = Post::findOrFail($post_id);
            if(Auth::id() === $post->user_id){
                return $next($request);
            }
        }elseif($type=='comment'){
            $comment_id = $request->route('comment');
            $comment = Comment::findOrFail($comment_id);
            if(Auth::id() === $comment->user_id){
                return $next($request);
            }
        }else{
            redirect()->route('welcome');
        }
    }

    protected function isSwaggerRequest($request)
    {
        return $request->is('docs/*') || $request->is('api/documentation') || $request->is('api/docs') || $request->is('docs');
    }
}
