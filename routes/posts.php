<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::resource('posts', PostController::class)
->middleware('check.author:post')
->except([
    'index', 'show','create'
])->names([
    'posts.edit'                    =>'edit',
    'posts.update'                  =>'update',
    'posts.delete'                  =>'destroy'
]);

Route::get('posts/create',[PostController::class,'create'])->name('posts.create');
Route::get('posts/show/{post:slug}',[PostController::class,'show'])->name('posts.show');
Route::get('posts',[PostController::class,'index'])->name('posts.index');

