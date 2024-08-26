<?php

namespace App\Models;

use App\Casts\HumanReadableTimeCast;
use App\Models\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','slug','content','excerpt',
        'user_id','featured_image','meta_title',
        'meta_description','meta_keywords'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new ActiveScope);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function author(){
        return $this->belongsTo(User::class);
    }

    protected $casts =[
        'created_at'                =>HumanReadableTimeCast::class,
        'meta_keywords' => 'array',
    ];


    public function getMetaDataAttribute($value)
    {
        return json_decode($value, true); // Custom decoding if needed
    }


    public function setMetaDataAttribute($value)
    {
        $this->attributes['meta_data'] = json_encode($value); // Custom encoding if needed
    }

    public static function ValidatePost($request , $id = Null){
        return $request->validate([
            'title'                      =>[
                'required','string','max:255','min:3',Rule::unique('posts','title')->ignore($id)
            ],
            'excerpt'                   =>"nullable|string|min:3|max:255",
            'content'                   =>'nullable|string',
            'meta_title'                =>'nullable|string|max:255',
            'meta_description'          =>'nullable|string',
            'meta_keywords'             =>'nullable|string',
            'featured_image'            =>['nullable','mimes:jpg,bmp,png','max:2048'],
            'status'                    =>['required',Rule::in('active','inactive','archived')],
        ]);
    }
}
