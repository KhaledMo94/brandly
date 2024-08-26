<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth','verified')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    require __DIR__.'/posts.php';
    require __DIR__.'/comments.php';
});
require __DIR__.'/auth.php';

// Route::get('/test-mailgun', function () {
//     Mail::raw('This is a test email to check Mailgun configuration.', function ($message) {
//         $message->to('khaledyoosef94@gmail.com')
//                 ->subject('Test Email');
//     });

//     return 'Test email sent!';
// });