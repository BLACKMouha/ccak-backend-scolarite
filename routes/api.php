<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\GuardianController;
use App\Http\Controllers\Student\DocumentController;

Route::middleware('auth:api')->group(function () {
    // Basic protected endpoints
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

    // Academic resources
    Route::apiResource('faculties', \App\Http\Controllers\Academic\FacultyController::class);
    Route::apiResource('departments', \App\Http\Controllers\Academic\DepartmentController::class);
    Route::apiResource('academic-programs', \App\Http\Controllers\Academic\AcademicProgramController::class);
    Route::apiResource('course-units', \App\Http\Controllers\Academic\CourseUnitController::class);
    Route::apiResource('courses', \App\Http\Controllers\Academic\CourseController::class);

    // Student area
    Route::apiResource('students', StudentController::class);

    // Nested guardians for students: /api/v1/students/{student}/guardians
    Route::apiResource('students.guardians', GuardianController::class);

    // Documents: nested index/store/show/update/destroy under students and review route
    Route::get('students/{student}/documents', [DocumentController::class, 'index']);
    Route::post('students/{student}/documents', [DocumentController::class, 'store']);
    Route::get('students/{student}/documents/{document}', [DocumentController::class, 'show']);
    Route::put('students/{student}/documents/{document}', [DocumentController::class, 'update']);
    Route::delete('students/{student}/documents/{document}', [DocumentController::class, 'destroy']);

    // Review endpoint (shallow): /api/v1/documents/{id}/review
    Route::put('documents/{document}/review', [DocumentController::class, 'review']);

    // Admin roles
    Route::get('roles', [RoleController::class, 'index']);
    Route::post('roles', [RoleController::class, 'store']);
    Route::put('roles/{role}', [RoleController::class, 'update']);
    Route::put('users/{user}/roles', [UserRoleController::class, 'update']);
});

// Routes de test sans authentification
Route::prefix('test')->group(function () {
    Route::apiResource('students', StudentController::class);
});
