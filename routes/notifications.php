<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NotificationController;
use App\Notifications\RealTimeNotification;

/*
|--------------------------------------------------------------------------
| Notification Routes
|--------------------------------------------------------------------------
|
| Here is where you can register notification routes for your application.
|
*/

Route::middleware(['auth'])->group(function () {
    // API Endpoints for fetching and managing notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark_as_read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark_all_as_read');
});

// Test notification route (can be restricted to local environment if needed)
Route::get('/test-notification', function () {
    if (!Auth::check()) {
        return 'أنت غير مسجل الدخول من منظور هذا المسار! يرجى التأكد من أنك مسجل الدخول في نفس المتصفح والرابط (localhost).';
    }

    /** @var \App\Models\User $user */
    $user = Auth::user();
    
    $user->notify(new RealTimeNotification(
        title: 'تنبيه تجريبي',
        message: 'هذا إشعار لحظي تم إرساله بنجاح عبر Laravel Reverb!',
        type: 'success',
        actionUrl: route('dashboard')
    ));

    return 'تم إرسال الإشعار! افحص الجرس في الأعلى. (المستخدم: ' . $user->name . ')';
});
