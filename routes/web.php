<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Mindora Platform
|--------------------------------------------------------------------------
*/

// Public Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.submit');
});

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management & Temporary Unlock Routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/unlock', [\App\Http\Controllers\ProfileController::class, 'unlock'])->name('profile.unlock');
    Route::post('/profile/lock', [\App\Http\Controllers\ProfileController::class, 'lock'])->name('profile.lock');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Assessment Catalog, Taking Flow, and Results
    Route::get('/assessments', [\App\Http\Controllers\AssessmentController::class, 'index'])->name('assessments.index');
    Route::get('/assessments/{assessment}/instructions', [\App\Http\Controllers\AssessmentController::class, 'instructions'])->name('assessments.instructions');
    Route::post('/assessments/{assessment}/start', [\App\Http\Controllers\AssessmentController::class, 'start'])->name('assessments.start');
    Route::get('/assessments/sessions/{session}', [\App\Http\Controllers\AssessmentController::class, 'take'])->name('assessments.take');
    Route::post('/assessments/sessions/{session}/answer', [\App\Http\Controllers\AssessmentController::class, 'saveAnswer'])->name('assessments.answer');
    Route::post('/assessments/sessions/{session}/finish', [\App\Http\Controllers\AssessmentController::class, 'finish'])->name('assessments.finish');
    Route::get('/assessments/results/{score}', [\App\Http\Controllers\AssessmentController::class, 'result'])->name('assessments.result');

    // AI Assistant Consultation Routes (Rate-Limited, Zero PII)
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/conversation/{score?}', [\App\Http\Controllers\AIController::class, 'getConversation'])->name('conversation');
        Route::post('/conversations/{conversation}/message', [\App\Http\Controllers\AIController::class, 'sendMessage'])->middleware('throttle:15,1')->name('message');
        Route::delete('/conversations/{conversation}/history', [\App\Http\Controllers\AIController::class, 'clearHistory'])->name('clear');
    });

    // Admin & Super Admin Protected Routes
    Route::middleware(['role:super_admin,admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
        Route::post('/users/{user}/unlock', [AdminDashboardController::class, 'unlockUserSensitiveData'])->name('users.unlock');
        Route::post('/users/lock', [AdminDashboardController::class, 'lockUserSensitiveData'])->name('users.lock');
        Route::get('/questions', [AdminDashboardController::class, 'questions'])->name('questions');
        Route::put('/questions/{question}', [AdminDashboardController::class, 'updateQuestion'])->name('questions.update');
        Route::get('/models', [AdminDashboardController::class, 'models'])->name('models');
        Route::put('/models/{model}/toggle', [AdminDashboardController::class, 'toggleModelStatus'])->name('models.toggle');
        Route::get('/audit-logs', [AdminDashboardController::class, 'auditLogs'])->name('audit-logs');
    });
});
