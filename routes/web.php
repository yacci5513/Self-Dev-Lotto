<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LottoController;
use Illuminate\Support\Facades\Route;

// 공개 라우트 (인증 불필요)
Route::get('/', [AuthController::class, 'dashboard'])->name('dashboard');

// 게스트 전용 라우트 (로그인하지 않은 사용자만 접근 가능)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::post('/check-email', [AuthController::class, 'checkEmail'])->name('check.email');
});

// 인증된 사용자 전용 라우트
Route::middleware('auth:sanctum')->group(function () {
    // 인증 관련
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 로또 관련
    Route::prefix('lotto')->name('lotto.')->group(function () {
        Route::get('/recommend', [LottoController::class, 'recommend'])->name('recommend');
        Route::post('/generate', [LottoController::class, 'generate'])->name('generate');
        Route::get('/history', [LottoController::class, 'history'])->name('history');
    });
});

// 404 리다이렉트
Route::fallback(function () {
    return redirect('/');
});
