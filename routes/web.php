<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DokterController;

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
    Route::post('/kontrol-survey', [PatientController::class, 'submitKontrolSurvey'])->name('patient.kontrol.survey');
    Route::get('/profile', [PatientController::class, 'showProfile'])->name('patient.profile');
    Route::post('/profile', [PatientController::class, 'updateProfile']);
});

// 3b. Dokter IGD Dashboard (akun bersama, dibuatkan oleh Admin)
Route::prefix('dokter')->group(function () {
    Route::get('/', [DokterController::class, 'index'])->name('dokter.dashboard');
    Route::post('/verify/{id}', [DokterController::class, 'verify'])->name('dokter.verify');

    // Kelola Screening
    Route::get('/screening', [DokterController::class, 'manageScreening'])->name('dokter.screening');
    Route::post('/screening', [DokterController::class, 'updateScreening']);

    // Kelola Penyakit
    Route::get('/penyakit', [DokterController::class, 'diseaseIndex'])->name('dokter.penyakit');
    Route::post('/penyakit', [DokterController::class, 'diseaseStore'])->name('dokter.penyakit.store');
    Route::post('/penyakit/{id}', [DokterController::class, 'diseaseUpdate'])->name('dokter.penyakit.update');
    Route::post('/penyakit/{id}/delete', [DokterController::class, 'diseaseDestroy'])->name('dokter.penyakit.delete');
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

    // Data Pasien lengkap + monitoring login gagal
    Route::get('/patients', [AdminController::class, 'patientIndex'])->name('admin.patients');

    // Kelola Akun (manajemen seluruh akun: Admin, Dokter IGD, Pasien)
    Route::get('/accounts', [AdminController::class, 'accountIndex'])->name('admin.accounts');
    Route::post('/accounts', [AdminController::class, 'accountStore'])->name('admin.accounts.store');
    Route::post('/accounts/{id}', [AdminController::class, 'accountUpdate'])->name('admin.accounts.update');
    Route::post('/accounts/{id}/reset-password', [AdminController::class, 'accountResetPassword'])->name('admin.accounts.reset');
    Route::post('/accounts/{id}/delete', [AdminController::class, 'accountDestroy'])->name('admin.accounts.delete');
    
    // Profit Metrics
    Route::get('/profit', [AdminController::class, 'profitReport'])->name('admin.profit.report');

    // Configurations, WhatsApp Gateway Fonnte Logs & Audits
    Route::get('/settings', [AdminController::class, 'systemSettings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'updateSettings']);
    Route::post('/settings/test-whatsapp', [AdminController::class, 'testWhatsApp'])->name('admin.settings.test-whatsapp');
});
