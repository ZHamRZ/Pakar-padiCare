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


// 🔹 Redirect root
Route::get('/', [UserDashboard::class, 'index'])->name('home');


// 🔹 AUTH
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/login/admin', [AuthController::class, 'adminLogin'])->name('login.admin.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout.get');

Route::middleware('auth')->group(function () {
    Route::get('/user/profile', [ProfileController::class, 'edit'])->name('user.profile.edit');
    Route::put('/user/profile', [ProfileController::class, 'update'])->name('user.profile.update');
    Route::get('/admin/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/admin/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::post('/email/verification-notification', [ProfileController::class, 'sendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', [ProfileController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verification.verify');
    Route::get('/email/verify-by-token/{id}/{hash}', [ProfileController::class, 'verifyEmailByToken'])
        ->name('verification.verifyByToken');
});


// 🔹 USER (PETANI)
Route::prefix('user')
    ->name('user.')
    ->group(function () {

        Route::get('/dashboard', [UserDashboard::class, 'index'])->name('dashboard');

        Route::get('/diagnosis', [DiagnosisController::class, 'index'])->name('diagnosis.index');
        Route::get('/diagnosis/identifikasi', [DiagnosisController::class, 'hasilIdentifikasi'])->name('diagnosis.hasil');
        Route::post('/diagnosis/identifikasi', [DiagnosisController::class, 'identifikasi'])->name('diagnosis.identifikasi');
        Route::post('/diagnosis/proses', [DiagnosisController::class, 'proses'])->name('diagnosis.proses');

        Route::get('/rekomendasi/preview', [RekomendasiController::class, 'preview'])->name('rekomendasi.preview');
        Route::get('/rekomendasi/preview/detail', [RekomendasiController::class, 'previewDetail'])->name('rekomendasi.preview.detail');
        Route::get('/rekomendasi/preview/cetak', [RekomendasiController::class, 'previewCetak'])->name('rekomendasi.preview.cetak');

        Route::middleware(['auth', 'role:petani'])->group(function () {
            Route::get('/rekomendasi/{id}', [RekomendasiController::class, 'show'])->name('rekomendasi.show');
            Route::get('/rekomendasi/{id}/detail', [RekomendasiController::class, 'detail'])->name('rekomendasi.detail');
            Route::get('/rekomendasi/{id}/cetak', [RekomendasiController::class, 'cetak'])->name('rekomendasi.cetak');
            Route::get('/riwayat', [UserRiwayat::class, 'index'])->name('riwayat.index');
        });
    });


// 🔹 ADMIN
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        Route::resource('penyakit', PenyakitController::class)->except(['show']);
        Route::resource('gejala', GejalaController::class)->except(['show']);
        Route::resource('pupuk', PupukController::class)->except(['show']);
        Route::resource('pestisida', PestisidaController::class)->except(['show']);

        Route::get('/kriteria', [KriteriaController::class, 'index'])->name('kriteria.index');
        Route::get('/kriteria/{kriteria}/edit', [KriteriaController::class, 'edit'])->name('kriteria.edit');
        Route::put('/kriteria/{kriteria}', [KriteriaController::class, 'update'])->name('kriteria.update');

        Route::get('/rating/pupuk', [RatingController::class, 'pupuk'])->name('rating.pupuk');
        Route::post('/rating/pupuk', [RatingController::class, 'simpanPupuk'])->name('rating.pupuk.simpan');

        Route::get('/rating/pestisida', [RatingController::class, 'pestisida'])->name('rating.pestisida');
        Route::post('/rating/pestisida', [RatingController::class, 'simpanPestisida'])->name('rating.pestisida.simpan');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');

        Route::get('/riwayat', [AdminRiwayat::class, 'index'])->name('riwayat.index');
        Route::get('/riwayat/{id}', [AdminRiwayat::class, 'show'])->name('riwayat.show');
        Route::get('/riwayat/{id}/detail', [AdminRiwayat::class, 'detail'])->name('riwayat.detail');
    });
