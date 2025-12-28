<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Notification\AnnouncementController;

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'message' => 'Operation successful',
            'meta' => null,
        ]);
    });
    Route::get('/protected-resource', function () {
        return response()->json([
            'success' => true,
            'data' => ['message' => 'This is a protected resource accessible only to authenticated Keycloak users.'],
            'message' => 'Operation successful',
            'meta' => null,
        ]);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']); // NOT-008
        Route::post('/', [NotificationController::class, 'send'])->middleware('role:ADMIN'); // NOT-007
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']); // NOT-009
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']); // NOT-010
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });

    // Announcements
    Route::prefix('announcements')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index']); // NOT-012
        Route::post('/', [AnnouncementController::class, 'store'])->middleware('role:ADMIN'); // NOT-011
        Route::get('/{id}', [AnnouncementController::class, 'show']);
        Route::put('/{id}', [AnnouncementController::class, 'update'])->middleware('role:ADMIN');
        Route::delete('/{id}', [AnnouncementController::class, 'destroy'])->middleware('role:ADMIN');
        Route::post('/{id}/dismiss', [AnnouncementController::class, 'dismiss']);
        Route::post('/{id}/publish', [AnnouncementController::class, 'publish'])->middleware('role:ADMIN');
    });

    Route::apiResource('faculties', \App\Http\Controllers\Academic\FacultyController::class);
    Route::apiResource('departments', \App\Http\Controllers\Academic\DepartmentController::class);
    Route::apiResource('academic-programs', \App\Http\Controllers\Academic\AcademicProgramController::class);
    Route::apiResource('course-units', \App\Http\Controllers\Academic\CourseUnitController::class);
    Route::apiResource('courses', \App\Http\Controllers\Academic\CourseController::class);

    Route::get('roles', [RoleController::class, 'index']);
    Route::post('roles', [RoleController::class, 'store']);
    Route::put('roles/{role}', [RoleController::class, 'update']);
    Route::put('users/{user}/roles', [UserRoleController::class, 'update']);
});
