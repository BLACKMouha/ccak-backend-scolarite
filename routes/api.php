<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('keycloak')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/protected-resource', function () {
        return response()->json(['message' => 'This is a protected resource accessible only to authenticated Keycloak users.']);
    });

    Route::apiResource('faculties', \App\Http\Controllers\Academic\FacultyController::class);
    Route::apiResource('departments', \App\Http\Controllers\Academic\DepartmentController::class);
    Route::apiResource('academic-programs', \App\Http\Controllers\Academic\AcademicProgramController::class);
    Route::apiResource('course-units', \App\Http\Controllers\Academic\CourseUnitController::class);
    Route::apiResource('courses', \App\Http\Controllers\Academic\CourseController::class);
});
