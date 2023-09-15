<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\V1\Admin\AdminAuthController;
use App\Http\Controllers\V1\Admin\AdminUserController;
use App\Http\Controllers\Auth\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// routes defined for version 1
Route::prefix('v1')->group(function () {
    //jwt authenticated routes
    Route::middleware('jwt')->group(function () {
        //routes which will be allowed to authenticated admin users
        Route::middleware('admin')->group(function () {
            Route::prefix('admin')->group(function () {
                Route::put('/user-edit/{uuid}', [AdminUserController::class,
                'userEdit'])->name('admin.user.edit');
                Route::get('/user-listing', [AdminUserController::class,
                'userList'])->name('admin.user.list');
                Route::delete('/user-delete/{uuid}', [AdminUserController::class,
                'userDelete'])->name('admin.user.delete');
                Route::get('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
            });
        });

        //routes which will be allowed to authenticated admin users
        Route::prefix('user')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('user.view');
            Route::delete('/delete', [UserController::class, 'userDelete'])->name('user.delete');
            Route::put('/edit', [UserController::class, 'userEdit'])->name('user.edit');
            Route::get('/logout', [AuthController::class, 'logout'])->name('user.logout');
        });
    });

    //routes which are non authenticated routes for users having prefix user
    Route::prefix('user')->group(function () {
        Route::post('/create', [AuthController::class, 'register'])->name('user.register');
        Route::post('/login', [AuthController::class, 'login'])->name('user.login');
        Route::post('/forgot-password', [ForgotPasswordController::class,
        'sendResetLinkEmail'])->name('user.forgot_password');
        Route::post('/reset-password-token', [ResetPasswordController::class,
        'resetPassword'])->name('user.reset_password');
    });

    //routes which are non authenticated routes for users having prefix admin
    Route::prefix('admin')->group(function () {
        Route::post('/create', [AdminAuthController::class, 'register'])->name('admin.register');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login');
    });
});
