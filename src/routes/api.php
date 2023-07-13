<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MobileAuthController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\GroupController as AdminGroupController;
use App\Http\Controllers\Admin\TodoController as AdminTodoController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
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

/**
 * SPA認証
 */
Route::get('/auth', [AuthController::class, 'getLoginUser']);

/**
 * APIトークン認証
 */
Route::post('register', [MobileAuthController::class, 'register']);
Route::post('login', [MobileAuthController::class, 'login'])->name('login');
Route::post('logout', [MobileAuthController::class, 'logout'])->middleware('auth:sanctum');


Route::group(['middleware' => 'auth:sanctum'], function () {
    /** Todo API */
    Route::resource('todos', TodoController::class, ['only' => ['index', 'store', 'update', 'destroy']]);
    /** グループ API */
    Route::resource('groups', GroupController::class, ['only' => ['index', 'store', 'update', 'destroy']]);
});

/**
 * 管理画面
 */
Route::prefix('admin')->group(function () {
    /**
     * SPA認証
     */
    Route::get('/', [AdminAuthController::class, 'getLoginAdmin']);
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::apiResource('groups', AdminGroupController::class);
        Route::apiResource('todos', AdminTodoController::class);
        Route::apiResource('users', AdminUserController::class);
        Route::post('/invite', [AdminAuthController::class, 'invite']);
    });
});
