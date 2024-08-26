<?php

namespace App\Models;

use App\Casts\HumanReadableTimeCast;
use App\Mail\CommentCreatedMail;
use App\Notifications\CommentCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content','user_id','post_id',
        'parent_id',
    ];

    public static function validateComment($request , $id = null){
        return $request->validate([
            'content'               =>'required|string',
            'post_id'               =>['required','integer',Rule::exists('posts','id')],
            'parent_id'             =>['nullable','integer',
                                        Rule::unique('comments','id')->ignore($id)],
        ]);
    }

    public function post(){
        return $this->belongsTo(Post::class);
    }

    public function author(){
        return $this->belongsTo(User::class);
    }

    public function children(){
        return $this->hasMany(Comment::class,'parent_id','id');
    }

    public function parent(){
        return $this->belongsTo(Comment::class , 'parent_id','id');
    }

    protected $casts =[
        'created_at'                =>HumanReadableTimeCast::class,
    ];

    protected static function booted()
    {
        static::created(function ($comment) {
            $post_author = $comment->post->user_id;
            if($post_author){
                Notification::send($post_author,new CommentCreated($comment));
            }
        });
    }
}
