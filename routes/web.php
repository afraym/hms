<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BedController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientVisitController;    
use App\Http\Controllers\ProxyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;

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

            // Route to print a single patient label
            Route::get('patients/{patient}/label', [PatientController::class, 'printLabels'])->name('patients.print.label');

            // Route to print multiple patient labels
            Route::get('patients/print/labels', [PatientController::class, 'printLabels'])->name('patients.print.labels');
        });

        Route::get('/proxy/national-id', [ProxyController::class, 'fetchNationalIdInfo']);
        Route::get('/api/check-national-id', [PatientController::class, 'checkNationalId'])->name('patients.checkNationalId');
        // Route::name('patients.')->group(function () {
            // Route::resource('patient.visits', PatientVisitController::class);
        // });
        Route::patch('/patients/{patient}/discharge', [PatientController::class, 'discharge'])->name('patients.discharge');
        Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
        Route::get('/patients/ajax-search', [PatientController::class, 'ajaxSearch'])->name('patients.ajaxSearch');
        Route::post('patients/{patient}/attachments', [PatientController::class, 'uploadAttachment'])->name('patients.attachments.upload');
        Route::post('/api/sync', [PatientController::class, 'sync'])->name('api.sync');
    });

// require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::middleware('role:admin')->group(function () {
    // Admin routes
});
