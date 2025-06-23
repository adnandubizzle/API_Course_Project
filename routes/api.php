

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// PUBLIC APIs---Accessable to anyone

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// We can group routes that require same middleware like snactum----GOOOOOD PROGRAMMING PRACRICE!
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/addTasks', [TaskController::class, 'store']);
    Route::get('/getTasks', [TaskController::class, 'index']);

    // In Progress
    Route::put('/tasks/{id}', [TaskController::class, 'update']); // Same?
    // Route::patch('/tasks/{id}', [TaskController::class, 'update']); // same same?!
    Route::delete('/tasks/{id}', [TaskController::class, 'delete']);
}
);
