<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/users', [UserController::class, 'index']);
Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
