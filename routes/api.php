<?php

use App\Http\Controllers\Academic\AcademicProgramController;
use App\Http\Controllers\Academic\CourseController;
use App\Http\Controllers\Academic\CourseUnitController;
use App\Http\Controllers\Academic\DepartmentController;
use App\Http\Controllers\Academic\FacultyController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GeneratedDocumentController;
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

    Route::apiResource('faculties', FacultyController::class);
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('academic-programs', AcademicProgramController::class);
    Route::apiResource('course-units', CourseUnitController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('generated-documents', GeneratedDocumentController::class);
    Route::get('/generated-documents/verify/{documentNumber}', [GeneratedDocumentController::class, 'verify']);
    Route::apiResource('documents', DocumentController::class);
    Route::prefix('documents')->group(function () {
        // Routes pour les rapports et statistiques
        Route::get('/report', [DocumentController::class, 'report']);
        Route::get('/check-status', [DocumentController::class, 'checkStatus']);
        Route::get('/check-status/{studentId}', [DocumentController::class, 'checkStatus']);

        // Routes pour les documents en attente
        Route::get('/pending', [DocumentController::class, 'pending']);

        // Routes pour les documents d'un étudiant spécifique
        Route::get('/student/{studentId}', [DocumentController::class, 'studentDocuments'])
            ->name('documents.student');

        // Routes spécifiques à un document (complémentaires aux routes apiResource)
        Route::prefix('{document}')->group(function () {
            // Téléchargement
            Route::get('/download', [DocumentController::class, 'download'])
                ->name('documents.download');
            Route::get('/download-file', [DocumentController::class, 'downloadFile'])
                ->name('documents.download.file');

            // Review (approbation/rejet)
            Route::post('/approve', [DocumentController::class, 'approve'])
                ->name('documents.approve');
            Route::post('/reject', [DocumentController::class, 'reject'])
                ->name('documents.reject');
        });
    });

    Route::get('roles', [RoleController::class, 'index']);
    Route::post('roles', [RoleController::class, 'store']);
    Route::put('roles/{role}', [RoleController::class, 'update']);
    Route::put('users/{user}/roles', [UserRoleController::class, 'update']);

    // Enrollments
    Route::get('/students/{id}/enrollments', [EnrollmentController::class, 'getByStudent']);

    // Academic Years
    Route::get('/academic-years/current', [AcademicYearController::class, 'current']);
    Route::put('/academic-years/{id}/set-current', [AcademicYearController::class, 'setCurrent']);

    // Course Enrollments
    Route::post('enrollments/{id}/courses', [CourseEnrollmentController::class, 'enrollCourse']);
    Route::get('enrollments/{id}/courses', [CourseEnrollmentController::class, 'getCourses']);
    Route::delete('enrollments/{enrollmentId}/courses/{courseEnrollmentId}', [CourseEnrollmentController::class, 'dropCourse']);
    Route::get('courses/{id}/availability', [CourseEnrollmentController::class, 'checkAvailability']);
    Route::get('programs/{id}/available-courses', [CourseEnrollmentController::class, 'getAvailableCoursesByProgram']);

});
