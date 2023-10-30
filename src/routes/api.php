<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\MobileAuthController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\GroupController as AdminGroupController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\ForgotPasswordController as AdminForgotPasswordController;
use App\Http\Controllers\Admin\ResetPasswordController as AdminResetPasswordController;
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

/** SPA認証 */
Route::get('/auth', [AuthController::class, 'getLoginUser']);
/** APIトークン認証 */
Route::post('register', [MobileAuthController::class, 'register']);
Route::post('login', [MobileAuthController::class, 'login']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [MobileAuthController::class, 'logout']);
    Route::get('login-user', [MobileAuthController::class, 'loginUser']);
});

/** ユーザー側 */
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('todos', TodoController::class, ['except' => ['show']]);
    Route::apiResource('groups', GroupController::class, ['except' => ['show']]);
    Route::group(['prefix' => 'groups'], function () {
        Route::get('/{group}/todos', [GroupController::class, 'todos']);
        Route::get('/{group}/users', [GroupController::class, 'users']);
        Route::post('/{group}/add_user', [GroupController::class, 'addUser']);
        Route::post('/{group}/delete_user', [GroupController::class, 'deleteUser']);
    });
    Route::put('/user/update', [UserController::class, 'update']);
    Route::apiResource('inquiries', InquiryController::class, ['only' => ['store']]);
});

/** 管理者側 */
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminAuthController::class, 'getLoginAdmin']);
    Route::post('/forgot-password', [AdminForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('/password/reset', [AdminResetPasswordController::class, 'reset']);

    Route::group(['middleware' => 'auth:admin'], function () {
        Route::apiResource('groups', AdminGroupController::class, ['except' => ['store']]);
        Route::apiResource('inquiries', AdminInquiryController::class, ['except' => ['store', 'destroy']]);
        Route::apiResource('todos', AdminTodoController::class, ['except' => ['store']]);
        Route::apiResource('users', AdminUserController::class, ['except' => ['store']]);
        Route::apiResource('admins', AdminController::class, ['except' => ['store']]);
        Route::post('/invite', [AdminAuthController::class, 'invite']);
        Route::post('/password/update', [AdminResetPasswordController::class, 'update']);
    });
});
