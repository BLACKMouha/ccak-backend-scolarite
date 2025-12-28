<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserRoleController;
use \App\Http\Controllers\Academic\DeliberationSessionController;

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


    // Deliberation Sessions
    Route::prefix('deliberation-sessions')->group(function () {
        Route::get('/', [DeliberationSessionController::class, 'index']);
        Route::post('/', [DeliberationSessionController::class, 'store']);
        Route::get('{id}', [DeliberationSessionController::class, 'show']);
        Route::put('{id}', [DeliberationSessionController::class, 'update']);
        Route::delete('{id}', [DeliberationSessionController::class, 'destroy']);
        Route::patch('{id}/status', [DeliberationSessionController::class, 'changeStatus']);
    });
    Route::post('deliberations/{deliberation_session}/start', [DeliberationSessionController::class, 'start']);
    Route::post('deliberations/{deliberation_session}/complete', [DeliberationSessionController::class, 'complete']);
    Route::get('deliberations/{deliberation_session}/students', [DeliberationSessionController::class, 'getStudents']);
    Route::get('deliberations/{deliberation_session}/minutes', [DeliberationSessionController::class, 'generateMinutes']);

    // Deliberation Results
    Route::apiResource('deliberation-results', \App\Http\Controllers\Academic\DeliberationResultController::class);

    // Student
    Route::apiResource('students', \App\Http\Controllers\Academic\StudentController::class);

    // Student Deliberation History
    Route::get('students/{student_id}/deliberations', [\App\Http\Controllers\Academic\StudentDeliberationController::class, 'history']);

    Route::apiResource('deliberation-sessions', \App\Http\Controllers\Academic\DeliberationSessionController::class);
    Route::apiResource('deliberation-results', \App\Http\Controllers\Academic\DeliberationResultController::class);
    Route::apiResource('faculty-members', \App\Http\Controllers\Academic\FacultyMemberController::class);

    // Faculties
    Route::apiResource('faculties', \App\Http\Controllers\Academic\FacultyController::class);


    Route::apiResource('departments', \App\Http\Controllers\Academic\DepartmentController::class);
    Route::apiResource('academic-programs', \App\Http\Controllers\Academic\AcademicProgramController::class);
    Route::apiResource('academic-years', \App\Http\Controllers\Academic\AcademicYearController::class);
    Route::apiResource('course-units', \App\Http\Controllers\Academic\CourseUnitController::class);
    Route::apiResource('courses', \App\Http\Controllers\Academic\CourseController::class);

    Route::get('roles', [RoleController::class, 'index']);
    Route::post('roles', [RoleController::class, 'store']);
    Route::put('roles/{role}', [RoleController::class, 'update']);
    Route::put('users/{user}/roles', [UserRoleController::class, 'update']);


});
