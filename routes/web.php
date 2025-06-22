<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BookMarkController;
use App\Http\Controllers\Admin\Books\AgricultureandLivelihoodBookController;
use App\Http\Controllers\Admin\Books\CulturalHeritageBookController;
use App\Http\Controllers\Admin\Books\EducationalBookController;
use App\Http\Controllers\Admin\Books\StoryBookController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\Users\AllUsersController;
use App\Http\Controllers\Admin\Users\VerifiedUsersController;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->middleware('guest')->name('welcome');

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/home', fn () => Inertia::render('Home'))->name('home');
});


Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/categories', [CategoriesController::class, 'index'])->name('categories');
    Route::post('/categories', [CategoriesController::class, 'store']);

    //Books
    //story books
    Route::get('/books/storybooks', [StoryBookController::class, 'index'])
    ->name('storybooks');
    Route::post('/books/storybooks', [StoryBookController::class, 'store']);
    Route::put('/books/storybooks/{id}', [StoryBookController::class, 'update']);
    Route::delete('/books/storybooks/{id}', [StoryBookController::class, 'destroy']);

    //educational books
    Route::get('/books/educational', [EducationalBookController::class, 'index'])
    ->name('educationalbooks');
    Route::post('books/educational', [EducationalBookController::class, 'store']);
    Route::put('/books/educational/{id}', [EducationalBookController::class, 'update']);
    Route::delete('/books/educational/{id}', [EducationalBookController::class, 'destroy']);

    //agriculture and livelihood books
    Route::get('/books/agricultureandlivelihood', [AgricultureandLivelihoodBookController::class, 'index'])
    ->name('agricultureandlivelihood');
    Route::post('/books/agricultureandlivelihood', [AgricultureandLivelihoodBookController::class, 'store']);
    Route::put('/books/agricultureandlivelihood/{id}', [AgricultureandLivelihoodBookController::class, 'update']);
    Route::delete('/books/agricultureandlivelihood/{id}', [AgricultureandLivelihoodBookController::class, 'destroy']);

    //cultural heritage books
    Route::get('/books/culturalheritage', [CulturalHeritageBookController::class, 'index'])
    ->name('culturalheritage');
    Route::post('/books/culturalheritage', [CulturalHeritageBookController::class, 'store']);
    Route::put('/books/culturalheritage/{id}', [CulturalHeritageBookController::class, 'update']);
    Route::delete('/books/culturalheritage/{id}', [CulturalHeritageBookController::class, 'destroy']);


    //users
    Route::get('/users/allusers', [AllUsersController::class, 'index'])->name('allusers');
    Route::put('/users/allusers/{id}', [AllUsersController::class, 'verify']);

    //Verified Users
    Route::get('/users/verifiedusers', [VerifiedUsersController::class, 'index'])->name('verifiedusers');
    //bokmarks
    Route::post('/bookmarks', [BookMarkController::class, 'store']);
});


require __DIR__.'/auth.php';