<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Redirect home page to dashboard or login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Documents (CRUD)
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::post('/documents/{document}/update', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    
    // Document Versions & Imports
    Route::post('/documents/{document}/restore-version/{versionId}', [DocumentController::class, 'restoreVersion'])->name('documents.restore-version');
    Route::post('/documents/{document}/import-docx', [DocumentController::class, 'importDocx'])->name('documents.import-docx');
    Route::post('/documents/{document}/import-pdf', [DocumentController::class, 'importPdf'])->name('documents.import-pdf');

    // Bilingual formatting helpers (AJAX)
    Route::post('/documents/api/format', [DocumentController::class, 'apiFormat'])->name('documents.api-format');

    // Templates (CRUD & Operations)
    Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/create', [TemplateController::class, 'create'])->name('templates.create');
    Route::post('/templates', [TemplateController::class, 'store'])->name('templates.store');
    Route::get('/templates/{template}/edit', [TemplateController::class, 'edit'])->name('templates.edit');
    Route::put('/templates/{template}', [TemplateController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{template}', [TemplateController::class, 'destroy'])->name('templates.destroy');
    
    // Merge Form
    Route::get('/templates/{template}/fill', [TemplateController::class, 'fillForm'])->name('templates.fill');
    Route::post('/templates/{template}/merge', [TemplateController::class, 'merge'])->name('templates.merge');

    // Legacy Upload
    Route::get('/upload-legacy', [UploadController::class, 'showUpload'])->name('upload-legacy.show');
    Route::post('/upload-legacy', [UploadController::class, 'upload'])->name('upload-legacy.store');

    // Exports
    Route::get('/documents/{document}/export-docx', [ExportController::class, 'exportDocx'])->name('documents.export-docx');
    Route::get('/documents/{document}/export-pdf', [ExportController::class, 'exportPdf'])->name('documents.export-pdf');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});
