<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use function Termwind\render;

Route::middleware('guest')->group( function () {
    Route::get('/',function() {
        return inertia('Welcome');
    })->name('welcome');
});


//user routes
Route::middleware(['auth', 'role:user'])->group( function () {
    Route::get('/home', function () {
        return inertia('Home');
    })->name('home');
    
});

Route::middleware(['auth', 'role:admin'])->group( function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

require __DIR__. '/auth.php';