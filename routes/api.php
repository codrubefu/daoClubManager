<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ParentStudentController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ResolveTenant;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->middleware(ResolveTenant::class);
Route::post('/auth/refresh', [AuthController::class, 'refresh'])->middleware(ResolveTenant::class);

Route::middleware([ResolveTenant::class, 'auth:sanctum'])->group(function (): void {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::patch('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::get('/parents/me/children', [ParentStudentController::class, 'myChildren']);
    Route::get('/parents/{parent}/children', [ParentStudentController::class, 'index']);
    Route::post('/parents/{parent}/children/{student}', [ParentStudentController::class, 'link']);
    Route::delete('/parents/{parent}/children/{student}', [ParentStudentController::class, 'unlink']);
});
