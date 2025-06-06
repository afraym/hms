<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BedController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientVisitController;    
use App\Http\Controllers\ProxyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;

Route::view('/', 'welcome');



Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');
    Route::prefix('admin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])
    // ->middleware(['auth', 'verified'])
    ->name('admin.dashboard');
        Route::resource('beds', BedController::class);
        Route::resource('patients', PatientController::class);
        Route::resource('departments', DepartmentController::class);
    });

Route::get('/proxy/national-id', [ProxyController::class, 'fetchNationalIdInfo']);
Route::get('/api/check-national-id', [PatientController::class, 'checkNationalId'])->name('patients.checkNationalId');
// Route::name('patients.')->group(function () {
    // Route::resource('patient.visits', PatientVisitController::class);
// });
Route::patch('/patients/{patient}/discharge', [PatientController::class, 'discharge'])->name('patients.discharge');
Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
Route::get('/patients/ajax-search', [PatientController::class, 'ajaxSearch'])->name('patients.ajaxSearch');

require __DIR__.'/auth.php';
