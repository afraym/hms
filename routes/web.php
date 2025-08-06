<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BedController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientVisitController;    
use App\Http\Controllers\ProxyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AttachmentController; 
use App\Http\Controllers\UserController; // Add this import at the top

Route::redirect('/', '/admin/patients/create');



Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');
    
    Route::middleware(['auth', 'role:admin|manager|reception'])->group(function () {
        Route::prefix('admin')->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index'])
                ->name('admin.dashboard');
            Route::resource('beds', BedController::class);
            Route::resource('patients', PatientController::class);
            Route::resource('departments', DepartmentController::class);
            Route::resource('patient-visits', PatientVisitController::class)->names('patient_visits');

            // Add user management routes (admin only)
            Route::middleware('role:admin')->group(function () {
                Route::resource('users', UserController::class);
                Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
                Route::get('/users/ajax-search', [UserController::class, 'ajaxSearch'])->name('users.ajaxSearch');
            });

            // Patient visit routes
            Route::get('patients/{patient}/visits/create', [PatientController::class, 'createVisit'])->name('patients.visits.create');
            Route::post('patients/{patient}/visits', [PatientController::class, 'storeVisit'])->name('patients.visits.store');
            Route::get('patients/{patient}/visits/{visit}/edit', [PatientController::class, 'editVisit'])->name('patients.visits.edit');
            Route::put('patients/{patient}/visits/{visit}', [PatientController::class, 'updateVisit'])->name('patients.visits.update');
            Route::delete('patients/{patient}/visits/{visit}', [PatientController::class, 'deleteVisit'])->name('patients.visits.delete');
            Route::get('patient-visits/{visit}', [PatientController::class, 'getVisit'])->name('patient-visits.show');

            // Route to print a single patient label
            // Route::get('patients/{patient}/label', [PatientController::class, 'printLabels'])->name('patients.print.label');

            // // Route to print multiple patient labels
            // Route::get('patients/print/labels', [PatientController::class, 'printLabels'])->name('patients.print.labels');
        });

        Route::get('/proxy/national-id', [ProxyController::class, 'fetchNationalIdInfo']);
        Route::get('/api/check-national-id', [PatientController::class, 'checkNationalId'])->name('patients.checkNationalId');
        // Route::name('patients.')->group(function () {
            // Route::resource('patient.visits', PatientVisitController::class);
        // });
        Route::patch('/patients/{patient}/discharge', [PatientController::class, 'discharge'])->name('patients.discharge');
        Route::patch('/patients/{patient}/deceased', [PatientController::class, 'markDeceased'])->name('patients.deceased');
        Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
        Route::get('/patients/ajax-search', [PatientController::class, 'ajaxSearch'])->name('patients.ajaxSearch');
        Route::post('patients/{patient}/attachments', [AttachmentController::class, 'store'])->name('patients.attachments.upload');
        Route::delete('patients/{patient}/attachments/{attachment}', [PatientController::class, 'deleteAttachment'])->name('patients.attachments.delete');
        Route::post('/api/sync', [PatientController::class, 'sync'])->name('api.sync');
        Route::get('/generate-medical-id', [PatientController::class, 'generateNewMedicalId'])->name('generate.medical.id');
        
        // Chart data API routes
        Route::get('/api/charts/weekly-patients', [DashboardController::class, 'getWeeklyPatientsData'])->name('charts.weekly.patients');
        Route::get('/api/charts/monthly-beds', [DashboardController::class, 'getMonthlyBedsData'])->name('charts.monthly.beds');
        Route::get('/api/charts/daily-visits', [DashboardController::class, 'getDailyVisitsData'])->name('charts.daily.visits');
        Route::get('patients/export', [PatientController::class, 'export'])->name('patients.export');
        // Route::post('/patients/{patient}/restore', [PatientController::class, 'restore'])->name('patients.restore');
        Route::get('/patients/trashed', [PatientController::class, 'trashed'])->name('patients.trashed');
        Route::get('/patients/import', [PatientController::class, 'importForm'])->name('patients.importForm');
        Route::post('/patients/import', [PatientController::class, 'import'])->name('patients.import');

        // Trashed patients routes
        Route::get('/patients/trashed', [PatientController::class, 'trashed'])->name('patients.trashed');
        Route::post('/patients/{id}/restore', [PatientController::class, 'restore'])->name('patients.restore');
        Route::delete('/patients/{id}/force-delete', [PatientController::class, 'forceDelete'])->name('patients.force-delete');
        Route::get('/patients/{id}/edit-trashed', [PatientController::class, 'editTrashed'])->name('patients.edit-trashed');
        Route::put('/patients/{id}/update-trashed', [PatientController::class, 'updateTrashed'])->name('patients.update-trashed');
    });

    // Route::middleware(['auth', 'checkrole:admin|manager'])->group(function () {
    //     Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    // });

// require __DIR__.'/auth.php';

Auth::routes();

Route::redirect('/home', '/admin/patients/create')->name('home');
Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::middleware('role:admin')->group(function () {
    // Admin routes
});

Route::get('/update-companion-details', [PatientVisitController::class, 'updateCompanionDetails']);
