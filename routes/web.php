<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\WebAuthController;

// Routes principales de l'application

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [WebAuthController::class, 'register']);

Route::get('/logout', function () {
    Auth::logout();
    session()->flush();
    session()->regenerate();
    return redirect('/login')->with('message', 'Déconnecté avec succès');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/posts', function () {
        return view('posts.index');
    })->name('posts.index');
    
    Route::get('/invitations', function () {
        return view('invitations.index');
    })->name('invitations.index');
});