<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BookMarkController;
use App\Http\Controllers\Admin\Books\AgricultureandLivelihoodBookController;
use App\Http\Controllers\Admin\Books\CulturalHeritageBookController;
use App\Http\Controllers\Admin\Books\EducationalBookController;
use App\Http\Controllers\Admin\Books\StoryBookController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\Users\AllUsersController;
use App\Http\Controllers\Admin\Users\VerifiedUsersController;
use App\Http\Controllers\Admin\Users\UnVerifiedUsersController;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->middleware('guest')->name('welcome');

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/home', fn () => Inertia::render('Home'))->name('home');
});


Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
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

    // Unverified Users
    Route::get('/users/unverifiedusers', [UnVerifiedUsersController::class, 'index'])->name('unverifiedusers');
    Route::put('/users/unverifiedusers/{id}', [UnVerifiedUsersController::class, 'verify']);
    Route::delete('/users/unverifiedusers/{id}', [UnVerifiedUsersController::class, 'destroy']);

    //Verified Users
    Route::get('/users/verifiedusers', [VerifiedUsersController::class, 'index'])->name('verifiedusers');
    //bokmarks
    Route::post('/bookmarks', [BookMarkController::class, 'store']);

    //Admin Profile
    Route::get('/profile',[ProfileController::class, 'index'])->name('admin.profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('admin.profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('admin.profile.avatar');
    Route::post('/profile/send-verification', [ProfileController::class, 'sendVerificationEmail'])->name('admin.profile.sendVerification');
});


require __DIR__.'/auth.php';

// Add Laravel email verification routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return inertia('Auth/VerifyEmail');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect(Auth::user()->role === 'admin' ? '/profile' : '/home');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});