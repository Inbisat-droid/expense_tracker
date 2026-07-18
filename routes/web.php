<?php

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Guest routes - registration & login (for users not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes - only logged-in users can access the expense tracker
Route::middleware('auth')->group(function () {
    Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions', [TransactionController::class, 'list'])->name('transactions.list');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::delete('/transactions', [TransactionController::class, 'destroyAll'])->name('transactions.destroyAll');
});
