<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\User\DiagnosisController;
use App\Http\Controllers\User\RekomendasiController;
use App\Http\Controllers\User\RiwayatController as UserRiwayat;

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\PenyakitController;
use App\Http\Controllers\Admin\GejalaController;
use App\Http\Controllers\Admin\PupukController;
use App\Http\Controllers\Admin\PestisidaController;
use App\Http\Controllers\Admin\KriteriaController;
use App\Http\Controllers\Admin\RatingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RiwayatController as AdminRiwayat;


// ─────────────────────────────────────────────────────────────
// 🔹 ROOT
// ─────────────────────────────────────────────────────────────
Route::get('/', [UserDashboard::class, 'index'])->name('home');


// ─────────────────────────────────────────────────────────────
// 🔹 AUTH (Guest Only)
// ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',        [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',       [AuthController::class, 'login'])->name('login.post');
    Route::post('/login/admin', [AuthController::class, 'adminLogin'])->name('login.admin.post');

    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Reset Password Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Logout (POST diutamakan; GET sebagai fallback)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout',  [AuthController::class, 'logout'])->name('logout.get');
});


// ─────────────────────────────────────────────────────────────
// 🔹 PROFIL & VERIFIKASI EMAIL (Auth)
// Gabungan: route lama (user/admin terpisah) + route baru (unified)
// ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // -- Unified profile (dari kode baru) --
    // Digunakan jika view/controller sudah menggunakan route('profile.edit')
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // -- Legacy profile routes (tetap dipertahankan agar tidak merusak view lama) --
    Route::get('/user/profile',  [ProfileController::class, 'edit'])->name('user.profile.edit');
    Route::put('/user/profile',  [ProfileController::class, 'update'])->name('user.profile.update');
    Route::get('/admin/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/admin/profile', [ProfileController::class, 'update'])->name('admin.profile.update');

    // -- Verifikasi email via signed URL (Laravel bawaan) --
    Route::post('/email/verification-notification', [ProfileController::class, 'sendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/email/verify/{id}/{hash}', [ProfileController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verification.verify');
    Route::get('/email/verify-by-token/{id}/{hash}', [ProfileController::class, 'verifyEmailByToken'])
        ->name('verification.verifyByToken');

});

// Link ini dibuka dari email, jadi tidak boleh wajib login.
Route::get('/profile/verify/{token}', [ProfileController::class, 'verifyEmailByRandomToken'])
    ->name('profile.verify.email');


// ─────────────────────────────────────────────────────────────
// 🔹 USER / PETANI
// ─────────────────────────────────────────────────────────────
Route::prefix('user')
    ->name('user.')
    ->group(function () {

        Route::get('/dashboard', [UserDashboard::class, 'index'])->name('dashboard');

        // Diagnosis — boleh diakses publik (sebelum auth)
        Route::get('/diagnosis',              [DiagnosisController::class, 'index'])->name('diagnosis.index');
        Route::get('/diagnosis/identifikasi', [DiagnosisController::class, 'hasilIdentifikasi'])->name('diagnosis.hasil');
        Route::post('/diagnosis/identifikasi', [DiagnosisController::class, 'identifikasi'])->name('diagnosis.identifikasi');
        Route::post('/diagnosis/proses',      [DiagnosisController::class, 'proses'])->name('diagnosis.proses');

        // Preview rekomendasi — publik
        Route::get('/rekomendasi/preview',        [RekomendasiController::class, 'preview'])->name('rekomendasi.preview');
        Route::get('/rekomendasi/preview/detail', [RekomendasiController::class, 'previewDetail'])->name('rekomendasi.preview.detail');
        Route::get('/rekomendasi/preview/cetak',  [RekomendasiController::class, 'previewCetak'])->name('rekomendasi.preview.cetak');

        // Riwayat & detail rekomendasi — wajib login sebagai petani
        Route::middleware(['auth', 'role:petani'])->group(function () {
            Route::get('/rekomendasi/{id}',        [RekomendasiController::class, 'show'])->name('rekomendasi.show');
            Route::get('/rekomendasi/{id}/detail', [RekomendasiController::class, 'detail'])->name('rekomendasi.detail');
            Route::get('/rekomendasi/{id}/cetak',  [RekomendasiController::class, 'cetak'])->name('rekomendasi.cetak');
            Route::get('/riwayat',                 [UserRiwayat::class, 'index'])->name('riwayat.index');
        });
    });


// ─────────────────────────────────────────────────────────────
// 🔹 ADMIN
// ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Master data
        Route::resource('penyakit',  PenyakitController::class)->except(['show']);
        Route::resource('gejala',    GejalaController::class)->except(['show']);
        Route::resource('pupuk',     PupukController::class)->except(['show']);
        Route::resource('pestisida', PestisidaController::class)->except(['show']);

        // Kriteria
        Route::get('/kriteria',                    [KriteriaController::class, 'index'])->name('kriteria.index');
        Route::post('/kriteria/update-bulk',       [KriteriaController::class, 'updateBulk'])->name('kriteria.updateBulk');
        Route::get('/kriteria/{kriteria}/edit',    [KriteriaController::class, 'edit'])->name('kriteria.edit');
        Route::put('/kriteria/{kriteria}',         [KriteriaController::class, 'update'])->name('kriteria.update');

        // Rating Pupuk
        Route::get('/rating/pupuk',  [RatingController::class, 'pupuk'])->name('rating.pupuk');
        Route::post('/rating/pupuk', [RatingController::class, 'simpanPupuk'])->name('rating.pupuk.simpan');

        // Rating Pestisida
        Route::get('/rating/pestisida',  [RatingController::class, 'pestisida'])->name('rating.pestisida');
        Route::post('/rating/pestisida', [RatingController::class, 'simpanPestisida'])->name('rating.pestisida.simpan');

        // Manajemen User Petani
        Route::get('/users',                           [UserController::class, 'index'])->name('users.index');
        Route::delete('/users/{user}',                 [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/reset-password',    [UserController::class, 'resetPassword'])->name('users.resetPassword');
        // Verifikasi email manual oleh admin (dari kode baru)
        Route::post('/users/{user}/verify-email',      [UserController::class, 'verifyEmailManual'])->name('users.verify');

        // Riwayat diagnosis
        Route::get('/riwayat',          [AdminRiwayat::class, 'index'])->name('riwayat.index');
        Route::get('/riwayat/{id}/cetak', [AdminRiwayat::class, 'cetak'])->name('riwayat.cetak');
        Route::get('/riwayat/{id}',     [AdminRiwayat::class, 'show'])->name('riwayat.show');
        Route::get('/riwayat/{id}/detail', [AdminRiwayat::class, 'detail'])->name('riwayat.detail');
    });
