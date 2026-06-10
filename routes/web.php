<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MLController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TranscriptController;
use Illuminate\Support\Facades\Route;

// === PUBLIC ROUTES ===
Route::get('/', fn() => redirect('/cek-hasil'));
Route::get('/cek-hasil', [PublicController::class, 'cekHasil'])->name('cek-hasil');
Route::post('/cek-hasil', [PublicController::class, 'cekHasilSearch'])->name('cek-hasil.search');
Route::get('/cek-hasil/transkrip/{student}/pdf', [App\Http\Controllers\TranscriptController::class, 'downloadPdf'])->name('public.transcripts.pdf');

// === AUTH ROUTES ===
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// === ADMIN ROUTES ===
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    // Students CRUD
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    // Scores
    Route::get('/scores', [ScoreController::class, 'index'])->name('scores.index');
    Route::post('/scores', [ScoreController::class, 'store'])->name('scores.store');

    // Transcripts
    Route::get('/transcripts/{student}', [TranscriptController::class, 'show'])->name('transcripts.show');
    Route::get('/transcripts/{student}/pdf', [TranscriptController::class, 'downloadPdf'])->name('transcripts.pdf');

    // Questionnaires
    Route::get('/questionnaires', [QuestionnaireController::class, 'index'])->name('questionnaires.index');
    Route::post('/questionnaires', [QuestionnaireController::class, 'store'])->name('questionnaires.store');

    // ML Training
    Route::get('/training', [MLController::class, 'training'])->name('training.index');
    Route::post('/training', [MLController::class, 'train'])->name('training.train');
    Route::post('/training/import', [MLController::class, 'importTrainingData'])->name('training.import');
    Route::delete('/training/custom-data', [MLController::class, 'clearCustomData'])->name('training.clearCustom');
    Route::get('/training/template', [MLController::class, 'downloadTemplate'])->name('training.template');

    // Classifications
    Route::get('/classifications', [MLController::class, 'classifications'])->name('classifications.index');
    Route::post('/classifications', [MLController::class, 'classify'])->name('classifications.classify');

    // Results
    Route::get('/results', [ResultController::class, 'index'])->name('results.index');

    // Reports
    Route::get('/reports', [ReportController::class, 'adminIndex'])->name('reports.index');
    Route::get('/reports/csv', [ReportController::class, 'exportCsv'])->name('reports.csv');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/logo', [SettingController::class, 'uploadLogo'])->name('settings.logo');
    Route::delete('/settings/logo', [SettingController::class, 'deleteLogo'])->name('settings.logo.delete');
    Route::put('/settings/weights', [SettingController::class, 'updateWeights'])->name('settings.weights');
});

// === KEPSEK ROUTES ===
Route::middleware(['auth', 'role:kepsek'])->prefix('kepsek')->name('kepsek.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'kepsek'])->name('dashboard');
    Route::get('/reports', [ReportController::class, 'kepsekIndex'])->name('reports.index');
    Route::get('/reports/csv', [ReportController::class, 'exportCsv'])->name('reports.csv');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
});

