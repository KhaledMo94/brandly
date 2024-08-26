<?php

use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::resource('comments', CommentController::class)->except([
    'index', 'show','create'
])->names([
    'posts.edit'                    =>'edit',
    'posts.update'                  =>'update',
    'posts.delete'                  =>'destroy'
])->middleware('check.author:comment');

Route::get('comments/create',[CommentController::class,'create'])->name('comments.create');
