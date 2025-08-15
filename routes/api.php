<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;


Route::get('/login', function () {
    return response()->json([
        'code' => 401,
        'message' => 'Invalido. Por favor inicia sesión primero'
    ], 401);
})->name('login');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-2fa', [AuthController::class, 'verify2fa']);
Route::post('/resend-2fa', [AuthController::class, 'resend2fa']);


Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);



