<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AdminController;

// 1. Public Routes
Route::get('/', [PublicController::class, 'index'])->name('public.home');

// 2. Authentication & OTP Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/otp', [AuthController::class, 'showOtp'])->name('auth.otp');
Route::post('/otp', [AuthController::class, 'verifyOtp']);
Route::post('/otp/resend', [AuthController::class, 'resendOtp'])->name('auth.otp.resend');
Route::get('/otp/resend', [AuthController::class, 'resendOtp']); // fallback for ease of access
Route::any('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// 3. Patient Dashboard (Auth checked inside controller)
Route::prefix('patient')->group(function () {
    Route::get('/', [PatientController::class, 'index'])->name('patient.dashboard');
    Route::post('/diagnosa', [PatientController::class, 'inputDiagnosa'])->name('patient.diagnosa');
    Route::post('/screening/{id}', [PatientController::class, 'submitScreening'])->name('patient.screening');
    Route::post('/survey/{id}', [PatientController::class, 'submitSurvey'])->name('patient.survey');
    Route::get('/profile', [PatientController::class, 'showProfile'])->name('patient.profile');
    Route::post('/profile', [PatientController::class, 'updateProfile']);
});

// 4. Admin Dashboard (Auth checked inside controller)
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Landing Manager
    Route::get('/landing', [AdminController::class, 'manageLanding'])->name('admin.landing');
    Route::post('/landing', [AdminController::class, 'updateLanding']);
    Route::post('/schedules', [AdminController::class, 'updateSchedules'])->name('admin.schedules');
    Route::post('/services', [AdminController::class, 'updateServices'])->name('admin.services');

    // Screening Manager
    Route::get('/screening', [AdminController::class, 'manageScreening'])->name('admin.screening');
    Route::post('/screening', [AdminController::class, 'updateScreening']);

    // Patients Diagnoses & Surveys Index
    Route::get('/diagnoses', [AdminController::class, 'diagnosisIndex'])->name('admin.diagnoses.index');
    Route::get('/diagnoses/{id}', [AdminController::class, 'diagnosisDetail'])->name('admin.diagnoses.detail');
    
    // Profit Metrics
    Route::get('/profit', [AdminController::class, 'profitReport'])->name('admin.profit.report');

    // Configurations, WhatsApp Gateway Fonnte Logs & Audits
    Route::get('/settings', [AdminController::class, 'systemSettings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'updateSettings']);
    Route::post('/settings/test-whatsapp', [AdminController::class, 'testWhatsApp'])->name('admin.settings.test-whatsapp');
});
