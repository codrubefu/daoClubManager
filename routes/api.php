<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TrainingTemplateController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ResolveTenant;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->middleware(ResolveTenant::class);
Route::post('/auth/refresh', [AuthController::class, 'refresh'])->middleware(ResolveTenant::class);

Route::middleware([ResolveTenant::class, 'auth:sanctum'])->group(function (): void {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);

    Route::apiResource('groups', GroupController::class);
    Route::post('/groups/{group}/assign-coach', [GroupController::class, 'assignCoach']);
    Route::post('/groups/{group}/assign-student', [GroupController::class, 'assignStudent']);

    Route::apiResource('training-templates', TrainingTemplateController::class);
});
