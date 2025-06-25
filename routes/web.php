<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BookMarkController;
use App\Http\Controllers\Admin\Books\AgricultureandLivelihoodBookController;
use App\Http\Controllers\Admin\Books\CulturalHeritageBookController;
use App\Http\Controllers\Admin\Books\EducationalBookController;
use App\Http\Controllers\Admin\Books\StoryBookController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\Users\AllUsersController;
use App\Http\Controllers\Admin\Users\VerifiedUsersController;
use App\Http\Controllers\Admin\Users\UnVerifiedUsersController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ReadingChallengeController;
use App\Http\Controllers\Admin\SuggestionController as AdminSuggestionController;
use App\Http\Controllers\Admin\ReviewModerationController;

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
    Route::post('/books/storybooks/reviews/{reviewId}/report', [StoryBookController::class, 'reportReview'])->name('books.storybooks.reviews.report');

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
    // Profile Activity Log for Activity Tab
    Route::get('/profile/activity-log', [ProfileController::class, 'activityLog'])->name('admin.profile.activity-log');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::put('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    // Recent Activity for Dashboard
    Route::get('/recent-activity', [NotificationController::class, 'recentActivity'])->name('notifications.recent-activity');

    // Analytics Dashboard
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');
    Route::get('/analytics/data', [AnalyticsController::class, 'data'])->name('admin.analytics.data');

    // Announcements (Admin)
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('admin.announcements');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('admin.announcements.store');
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('admin.announcements.destroy');

    // Reading Challenges (Admin)
    Route::get('/challenges', [ReadingChallengeController::class, 'index'])->name('admin.challenges');
    Route::post('/challenges', [ReadingChallengeController::class, 'store'])->name('admin.challenges.store');
    Route::delete('/challenges/{id}', [ReadingChallengeController::class, 'destroy'])->name('admin.challenges.destroy');

    // User suggestion submission
    Route::post('/admin/suggestions/{id}/status', [AdminSuggestionController::class, 'updateStatus'])->name('admin.suggestions.status');
    Route::post('/admin/suggestions/{id}/respond', [AdminSuggestionController::class, 'respond'])->name('admin.suggestions.respond');

    // Admin Review Moderation
    Route::get('/reviews/moderation', [ReviewModerationController::class, 'index'])->name('admin.reviews.moderation');
    Route::post('/reviews/{id}/approve', [ReviewModerationController::class, 'approve'])->name('admin.reviews.approve');
    Route::post('/reviews/{id}/reject', [ReviewModerationController::class, 'reject'])->name('admin.reviews.reject');
    Route::post('/reviews/{id}/dismiss-report', [ReviewModerationController::class, 'dismissReport'])->name('admin.reviews.dismissReport');
    Route::post('/reviews/bulk-action', [ReviewModerationController::class, 'bulkAction'])->name('admin.reviews.bulkAction');
});

// Public/user-facing announcements list (for dashboard/homepage)
Route::get('/announcements/list', [AnnouncementController::class, 'list']);

// User endpoints for challenges
Route::middleware(['auth'])->group(function () {
    Route::get('/challenges/list', [ReadingChallengeController::class, 'list']);
    Route::post('/challenges/{id}/join', [ReadingChallengeController::class, 'join']);
    Route::post('/challenges/{id}/progress', [ReadingChallengeController::class, 'progress']);
    Route::get('/challenges/{id}/leaderboard', [ReadingChallengeController::class, 'leaderboard']);
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

// Admin suggestion management
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/suggestions', [AdminSuggestionController::class, 'index'])->name('admin.suggestions');
});

// Public: List approved suggestions (Inertia page)
Route::get('/suggestions/approved', [AdminSuggestionController::class, 'publicList'])->name('suggestions.approved');