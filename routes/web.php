<?php
// routes/web.php - ROUTE LANGSUNG KE TABEL SAW

use App\Http\Controllers\SurveyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SurveyQuestionController;
use App\Http\Controllers\SurveyResultController;
use App\Http\Controllers\DynamicSurveyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\FooterLinkController;
use App\Http\Controllers\ContactInfoController;

use Illuminate\Support\Facades\Route;

// Public Survey Routes
Route::get('/', [SurveyController::class, 'index'])->name('survey.index');
Route::post('/survey', [SurveyController::class, 'store'])->name('survey.store');
Route::get('/dashboard', [SurveyController::class, 'dashboard'])->name('survey.dashboard');
Route::get('/export', [SurveyController::class, 'export'])->name('survey.export');

// Admin Authentication Routes
Route::get('/admin', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/auth', [AdminController::class, 'authenticate'])->name('admin.authenticate');
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// Admin Dashboard & Management Routes
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin/export', [AdminController::class, 'export'])->name('admin.export');
Route::delete('/admin/survey/{id}', [AdminController::class, 'deleteSurvey'])->name('admin.deleteSurvey');
Route::get('/admin/survey/{id}/detail', [AdminController::class, 'getSurveyDetail'])->name('admin.survey.detail');

// Admin File Management Routes
Route::get('/admin/files', [AdminController::class, 'uploadedFiles'])->name('admin.uploadedFiles');
Route::get('/admin/file/view/{id}', [AdminController::class, 'viewFile'])->name('admin.viewFile');
Route::get('/admin/file/download/{id}', [AdminController::class, 'downloadFile'])->name('admin.downloadFile');

// Admin Question Management Routes
Route::prefix('admin/questions')->name('admin.questions.')->group(function () {
    Route::get('/', [SurveyQuestionController::class, 'index'])->name('index');
    Route::get('/section/create', [SurveyQuestionController::class, 'createSection'])->name('create-section');
    Route::post('/section', [SurveyQuestionController::class, 'storeSection'])->name('store-section');
    Route::delete('/section/{id}', [SurveyQuestionController::class, 'deleteSection'])->name('delete-section');
    Route::put('/section/{id}/toggle', [SurveyQuestionController::class, 'toggleSection'])->name('toggle-section');
    Route::post('/sections/reorder', [SurveyQuestionController::class, 'updateSectionOrder'])->name('reorder-sections');
    Route::get('/section/{sectionId}/question/create', [SurveyQuestionController::class, 'createQuestion'])->name('create-question');
    Route::post('/section/{sectionId}/question', [SurveyQuestionController::class, 'storeQuestion'])->name('store-question');
    Route::get('/question/{id}/edit', [SurveyQuestionController::class, 'editQuestion'])->name('edit-question');
    Route::put('/question/{id}', [SurveyQuestionController::class, 'updateQuestion'])->name('update-question');
    Route::delete('/question/{id}', [SurveyQuestionController::class, 'deleteQuestion'])->name('delete-question');
    Route::put('/question/{id}/toggle', [SurveyQuestionController::class, 'toggleQuestion'])->name('toggle-question');
    Route::post('/section/{sectionId}/questions/reorder', [SurveyQuestionController::class, 'updateQuestionOrder'])->name('reorder-questions');
});

// HASIL SURVEY ROUTE - LANGSUNG KE TABEL SAW
Route::get('/admin/hasil-survey', [SurveyResultController::class, 'dashboard'])->name('admin.hasil-survey');

// Admin User Management Routes
Route::prefix('admin/users')->name('admin.users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{id}/edit-password', [UserController::class, 'editPassword'])->name('edit-password');
    Route::put('/{id}/password', [UserController::class, 'updatePassword'])->name('update-password');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
});

// Admin Asset Management Routes
Route::prefix('admin/assets')->name('admin.assets.')->group(function () {
    Route::get('/', [AssetController::class, 'index'])->name('index');
    Route::get('/create', [AssetController::class, 'create'])->name('create');
    Route::post('/', [AssetController::class, 'store'])->name('store');
    Route::put('/{id}/toggle', [AssetController::class, 'toggle'])->name('toggle');
    Route::delete('/{id}', [AssetController::class, 'destroy'])->name('destroy');
});

// Dynamic Survey Routes
Route::get('/survey/{slug?}', [DynamicSurveyController::class, 'showSurvey'])->name('dynamic.survey');
Route::post('/survey/submit', [DynamicSurveyController::class, 'submitSurvey'])->name('dynamic.survey.submit');

// Admin Footer Links Management Routes
Route::prefix('admin/footer-links')->name('admin.footer-links.')->group(function () {
    Route::get('/', [FooterLinkController::class, 'index'])->name('index');
    Route::get('/create', [FooterLinkController::class, 'create'])->name('create');
    Route::post('/', [FooterLinkController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [FooterLinkController::class, 'edit'])->name('edit');
    Route::put('/{id}', [FooterLinkController::class, 'update'])->name('update');
    Route::put('/{id}/toggle', [FooterLinkController::class, 'toggle'])->name('toggle');
    Route::delete('/{id}', [FooterLinkController::class, 'destroy'])->name('destroy');
    Route::post('/reorder', [FooterLinkController::class, 'updateOrder'])->name('reorder');
});

// Admin Contact Info Management Routes
Route::prefix('admin/contact-info')->name('admin.contact-info.')->group(function () {
    Route::get('/edit', [ContactInfoController::class, 'edit'])->name('edit');
    Route::put('/update', [ContactInfoController::class, 'update'])->name('update');
});