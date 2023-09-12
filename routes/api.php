<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\{AuthController, UserController, Admin\AdminAuthController, 
    Admin\AdminUserController};

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
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// routes defined for version 1
Route::prefix('v1')->group(function () {
    //jwt authenticated routes    
    Route::middleware(['jwt'])->group(function () {

        //routes which will be allowed to authenticated admin users
        Route::middleware(['admin'])->group(function () {
            Route::prefix('admin')->group(function(){
                Route::put('/user-edit/{uuid}', [AdminUserController::class, 'userEdit'])->name('admin.user.edit');
                Route::get('/user-listing', [AdminUserController::class, 'userList'])->name('admin.user.list');
                Route::delete('/user-delete/{uuid}', [AdminUserController::class, 'userDelete'])->name('admin.user.delete');
            });
        });

        //routes which will be allowed to authenticated admin users
        Route::prefix('user')->group(function(){
            Route::delete('/delete', [UserController::class, 'register'])->name('admin.user.delete');
        });
    });

    //routes which are non authenticated routes for users having prefix user
    Route::prefix('user')->group(function(){
        Route::post('/create', [AuthController::class, 'register'])->name('user.register');
        Route::post('/login', [AuthController::class, 'login'])->name('user.login');
    });

    //routes which are non authenticated routes for users having prefix admin
    Route::prefix('admin')->group(function(){
        Route::post('/create', [AdminAuthController::class, 'register'])->name('admin.register');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login');
    });
});