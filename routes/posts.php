<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::resource('posts', PostController::class)->except([
    'index', 'show','create'
])->names([
    'posts.edit'                    =>'edit',
    'posts.update'                  =>'update',
    'posts.delete'                  =>'destroy'
])->middleware('check.author:post');

Route::get('posts/create',[PostController::class,'create'])->name('posts.create');
Route::get('posts/show/{post:slug}',[PostController::class,'show'])->name('posts.show');
Route::get('/',[PostController::class,'index'])->name('posts.index');

