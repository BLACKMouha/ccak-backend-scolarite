<?php

use App\Http\Controllers\Academic\AcademicYearController;
use App\Http\Controllers\Academic\CourseEnrollmentController;
use App\Http\Controllers\Academic\EnrollmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Models\Course;

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

    Route::apiResource('faculties', \App\Http\Controllers\Academic\FacultyController::class);
    Route::apiResource('departments', \App\Http\Controllers\Academic\DepartmentController::class);
    Route::apiResource('academic-programs', \App\Http\Controllers\Academic\AcademicProgramController::class);
    Route::apiResource('course-units', \App\Http\Controllers\Academic\CourseUnitController::class);
    Route::apiResource('courses', \App\Http\Controllers\Academic\CourseController::class);
    Route::apiResource('academic-years', \App\Http\Controllers\Academic\AcademicYearController::class);
    Route::apiResource('enrollments', \App\Http\Controllers\Academic\EnrollmentController::class);
    Route::apiResource('course-enrollments', \App\Http\Controllers\Academic\CourseEnrollmentController::class);

    Route::get('roles', [RoleController::class, 'index']);
    Route::post('roles', [RoleController::class, 'store']);
    Route::put('roles/{role}', [RoleController::class, 'update']);
    Route::put('users/{user}/roles', [UserRoleController::class, 'update']);
});
