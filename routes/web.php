<?php

use App\Http\Controllers\AdminControllers\AdminDashboardController;
use App\Http\Controllers\AdminControllers\BookMarkController;
use App\Http\Controllers\AdminControllers\Books\AgricultureandLivelihoodBookController;
use App\Http\Controllers\AdminControllers\Books\CulturalHeritageBookController;
use App\Http\Controllers\AdminControllers\Books\EducationalBookController;
use App\Http\Controllers\AdminControllers\Books\StoryBookController;
use App\Http\Controllers\AdminControllers\NotificationController;
use App\Http\Controllers\AdminControllers\ProfileController;
use App\Http\Controllers\AdminControllers\Users\AllUsersController;
use App\Http\Controllers\AdminControllers\Users\VerifiedUsersController;
use App\Http\Controllers\AdminControllers\Users\UnVerifiedUsersController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminControllers\AnalyticsController;
use App\Http\Controllers\AdminControllers\AnnouncementController;
use App\Http\Controllers\AdminControllers\ReadingChallengeController;
use App\Http\Controllers\AdminControllers\SuggestionController as AdminSuggestionController;
use App\Http\Controllers\AdminControllers\ReviewModerationController;
use App\Http\Controllers\AdminControllers\DigitalArchiveController;
use App\Http\Controllers\UserControllers\BookBrowseController;
use App\Http\Controllers\UserControllers\ReviewController;
use App\Http\Controllers\UserControllers\ReadingProgressController;
use App\Http\Controllers\UserControllers\UserReadingChallengeController;
use App\Http\Controllers\UserControllers\HomeController;
use App\Http\Controllers\UserControllers\ProfileController as UserProfileController;



//User Routes
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/home', [HomeController::class, 'home'])->name('home');

    // User-facing book browsing routes
    Route::get('/books/story', [BookBrowseController::class, 'story'])->name('user.books.story');

    Route::get('/books/browse', [BookBrowseController::class, 'browse'])->name('books.browse');
    Route::get('/books/educational', [BookBrowseController::class, 'educational'])->name('user.books.educational');
    Route::get('/books/agriculture', [BookBrowseController::class, 'agriculture'])->name('user.books.agriculture');
    Route::get('/books/cultural', [BookBrowseController::class, 'cultural'])->name('user.books.cultural');

    // User-facing book reviews/ratings
    Route::get('/books/{book}/reviews', [ReviewController::class, 'index'])->name('user.books.reviews');
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store']);

    // User-facing reading progress
    Route::get('/books/progress', [ReadingProgressController::class, 'index'])->name('user.books.progress');
    Route::post('/books/{book}/progress', [ReadingProgressController::class, 'store']);

    // User-facing bookmarks
    Route::get('/bookmarks', [BookMarkController::class, 'index'])->name('user.bookmarks');
    Route::delete('/bookmarks/{book_id}', [BookMarkController::class, 'destroy']);

    //user-facing reading challenges
    Route::get('/challenges/list', [UserReadingChallengeController::class, 'list'])->name('user.challenges');
    Route::post('/challenges/{id}/join', [UserReadingChallengeController::class, 'join']);
    Route::post('/challenges/{id}/progress', [UserReadingChallengeController::class, 'progress']);
    Route::get('/challenges/{id}/leaderboard', [UserReadingChallengeController::class, 'leaderboard']);

    //user-facing profile
    Route::get('/user/profile', [UserProfileController::class, 'index'])->name('user.profile');
    Route::put('/user/profile', [UserProfileController::class, 'update']);
    Route::put('/user/profile/password', [UserProfileController::class, 'changePassword']);
    Route::post('/user/profile/avatar', [UserProfileController::class, 'uploadAvatar']);
    Route::get('/user/profile/complete', [UserProfileController::class, 'complete'])->name('user.profile.complete');
    Route::post('/user/profile/complete', [UserProfileController::class, 'storeComplete']);
    Route::get('/user/saved-books', [UserProfileController::class, 'savedBooks'])->name('user.savedbooks');

    // User book details page
    Route::get('/books/{id}', [App\Http\Controllers\UserControllers\BookBrowseController::class, 'show'])->name('user.books.show');
}); 


//Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    //Books
    //story books
    Route::get('/books/storybooks', [StoryBookController::class, 'index'])
        ->name('admin.storybooks');
    Route::post('/books/storybooks', [StoryBookController::class, 'store']);
    Route::put('/books/storybooks/{id}', [StoryBookController::class, 'update']);
    Route::delete('/books/storybooks/{id}', [StoryBookController::class, 'destroy']);
    Route::post('/books/storybooks/reviews/{reviewId}/report', [StoryBookController::class, 'reportReview'])->name('books.storybooks.reviews.report');

    //educational books
    Route::get('/books/educational', [EducationalBookController::class, 'index'])
        ->name('admin.educationalbooks');
    Route::post('books/educational', [EducationalBookController::class, 'store']);
    Route::put('/books/educational/{id}', [EducationalBookController::class, 'update']);
    Route::delete('/books/educational/{id}', [EducationalBookController::class, 'destroy']);

    //agriculture and livelihood books
    Route::get('/books/agricultureandlivelihood', [AgricultureandLivelihoodBookController::class, 'index'])
        ->name('admin.agricultureandlivelihood');
    Route::post('/books/agricultureandlivelihood', [AgricultureandLivelihoodBookController::class, 'store']);
    Route::put('/books/agricultureandlivelihood/{id}', [AgricultureandLivelihoodBookController::class, 'update']);
    Route::delete('/books/agricultureandlivelihood/{id}', [AgricultureandLivelihoodBookController::class, 'destroy']);

    //cultural heritage books
    Route::get('/books/culturalheritage', [CulturalHeritageBookController::class, 'index'])
        ->name('admin.culturalheritage');
    Route::post('/books/culturalheritage', [CulturalHeritageBookController::class, 'store']);
    Route::put('/books/culturalheritage/{id}', [CulturalHeritageBookController::class, 'update']);
    Route::delete('/books/culturalheritage/{id}', [CulturalHeritageBookController::class, 'destroy']);


    //users
    Route::get('/users/allusers', [AllUsersController::class, 'index'])->name('admin.allusers');
    Route::put('/users/allusers/{id}', [AllUsersController::class, 'verify']);

    // Unverified Users
    Route::get('/users/unverifiedusers', [UnVerifiedUsersController::class, 'index'])->name('admin.unverifiedusers');
    Route::put('/users/unverifiedusers/{id}', [UnVerifiedUsersController::class, 'verify']);
    Route::delete('/users/unverifiedusers/{id}', [UnVerifiedUsersController::class, 'destroy']);

    //Verified Users
    Route::get('/users/verifiedusers', [VerifiedUsersController::class, 'index'])->name('admin.verifiedusers');
    //bokmarks
    Route::post('/bookmarks', [BookMarkController::class, 'store']);

    //Admin Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'changePassword']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::post('/profile/send-verification', [ProfileController::class, 'sendVerificationEmail']);
    // Profile Activity Log for Activity Tab
    Route::get('/profile/activity-log', [ProfileController::class, 'activityLog'])->name('admin.profile.activity-log');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('admin.notifications.unread-count');
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.mark-as-read');
    Route::put('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'clearAll'])->name('admin.notifications.clear-all');
    // Recent Activity for Dashboard
    Route::get('/recent-activity', [NotificationController::class, 'recentActivity'])->name('admin.notifications.recent-activity');

    // Analytics Dashboard
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');
    Route::get('/analytics/data', [AnalyticsController::class, 'data']);

    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('admin.announcements');
    Route::post('/announcements', [AnnouncementController::class, 'store']);
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);

    // Reading Challenges (Admin)
    Route::get('/challenges', [ReadingChallengeController::class, 'index'])->name('admin.challenges');
    Route::post('/challenges', [ReadingChallengeController::class, 'store']);
    Route::delete('/challenges/{id}', [ReadingChallengeController::class, 'destroy']);

    // User suggestion submission
    Route::get('/suggestions', [AdminSuggestionController::class, 'index'])->name('admin.suggestions');
    Route::post('/suggestions/{id}/status', [AdminSuggestionController::class, 'updateStatus']);
    Route::post('/suggestions/{id}/respond', [AdminSuggestionController::class, 'respond']);

    // Admin Review Moderation
    Route::get('/reviews/moderation', [ReviewModerationController::class, 'index'])->name('admin.reviews');
    Route::post('/reviews/{id}/approve', [ReviewModerationController::class, 'approve']);
    Route::post('/reviews/{id}/reject', [ReviewModerationController::class, 'reject']);
    Route::post('/reviews/{id}/dismiss-report', [ReviewModerationController::class, 'dismissReport']);
    Route::post('/reviews/bulk-action', [ReviewModerationController::class, 'bulkAction']);

    // Digital Archive
    Route::get('/archive', [DigitalArchiveController::class, 'index'])->name('admin.archive');
    Route::post('/archive', [DigitalArchiveController::class, 'store']);
    Route::delete('/archive/{id}', [DigitalArchiveController::class, 'destroy']);
    Route::get('/archive/{id}/download', [DigitalArchiveController::class, 'download']);

    Route::get('/dashboard/activity-overview', [AdminDashboardController::class, 'activityOverview']);
});

// Public/user-facing announcements list (for dashboard/homepage)
Route::get('/announcements/list', [AnnouncementController::class, 'list']);

// User endpoints for challenges
// Route::middleware(['auth'])->group(function () {
//     Route::get('/challenges/list', [ReadingChallengeController::class, 'list']);
//     Route::post('/challenges/{id}/join', [ReadingChallengeController::class, 'join']);
//     Route::post('/challenges/{id}/progress', [ReadingChallengeController::class, 'progress']);
//     Route::get('/challenges/{id}/leaderboard', [ReadingChallengeController::class, 'leaderboard']);
// });

require __DIR__ . '/auth.php';

// Add Laravel email verification routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return inertia('Auth/VerifyEmail');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect(Auth::user()->role === 'admin' ? '/profile' : '/user/profile')->with('status', 'verified');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});


// Public: List approved suggestions (Inertia page)
Route::get('/suggestions/approved', [AdminSuggestionController::class, 'publicList'])->name('suggestions.approved');

// Public Digital Archive
Route::get('/public-archive', [\App\Http\Controllers\AdminControllers\DigitalArchiveController::class, 'publicIndex'])->name('public.archive.index');
Route::get('/public-archive/{id}/download', [\App\Http\Controllers\AdminControllers\DigitalArchiveController::class, 'publicDownload'])->name('public.archive.download');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/public-suggestions', [\App\Http\Controllers\UserControllers\SuggestionController::class, 'listPublic'])->name('public.suggestions');
    Route::post('/suggestions/{id}/upvote', [\App\Http\Controllers\UserControllers\SuggestionController::class, 'upvote'])->name('suggestions.upvote');
});


