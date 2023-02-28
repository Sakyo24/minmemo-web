<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MobileAuthController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/auth', [AuthController::class, 'getLoginUser']);

/**
 * APIトークン認証
 */
Route::post('register', [MobileAuthController::class, 'register']);
Route::post('login', [MobileAuthController::class, 'login'])->name('login');
Route::post('logout', [MobileAuthController::class, 'logout'])->middleware('auth:sanctum');

/**
 * Todo API
 */
Route::resource('todos', TodoController::class, ['only' => ['index', 'store', 'update', 'destroy']]);

/**
 * 管理画面
 */
Route::prefix('admin')->group(function () {
    /**
     * SPA認証
     */
    Route::get('/', [AdminAuthController::class, 'getLoginAdmin']);
});
