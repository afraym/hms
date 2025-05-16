<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BedController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProxyController;
use App\Http\Controllers\DashboardController;

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
    });

Route::get('/proxy/national-id', [ProxyController::class, 'fetchNationalIdInfo']);
Route::resource('patient_visits', \App\Http\Controllers\PatientVisitController::class);

require __DIR__.'/auth.php';
