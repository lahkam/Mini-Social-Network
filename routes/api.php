<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\Auth\AuthController;



// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum,web'])->group(function () {
    // User routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Post routes
    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'index']);
        Route::post('/', [PostController::class, 'store']);
        Route::post('/generate-ai', [PostController::class, 'generateAiPost']);
        Route::get('/user/{userId}', [PostController::class, 'getUserPosts']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
    });

    // Invitation routes
    Route::prefix('invitations')->group(function () {
        Route::post('/send', [InvitationController::class, 'sendInvitation']);
        Route::post('/{id}/accept', [InvitationController::class, 'acceptInvitation']);
        Route::post('/{id}/decline', [InvitationController::class, 'declineInvitation']);
        Route::post('/{id}/revoke', [InvitationController::class, 'revokeAccess']);
        Route::get('/pending', [InvitationController::class, 'getPendingInvitations']);
        Route::get('/sent', [InvitationController::class, 'getSentInvitations']);
        Route::get('/granted-access', [InvitationController::class, 'getGrantedAccess']);
    });
});