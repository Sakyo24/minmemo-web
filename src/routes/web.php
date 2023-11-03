<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\MobileAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * SPA認証
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

/**
 * 管理画面
 */
Route::prefix('admin')->group(function () {
    /**
     * SPA認証
     */
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout']);
    Route::get('/email/verify/{id}/{hash}', [AdminAuthController::class, 'verify'])
        ->middleware(['signed'])->name('admin.verification.verify');
});

Route::get('/email/verify/{id}/{hash}', [MobileAuthController::class, 'verify'])->middleware(['signed'])->name('user.verification.verify');

/**
 * SPA
 */
Route::get('/{any?}', function () {
    return view('index');
})->where('any', '.+');
