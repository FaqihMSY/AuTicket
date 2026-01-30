<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuditorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Breeze Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // AuTicket Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // AuTicket Projects
    Route::get('/projects/history', [ProjectController::class, 'history'])
        ->name('projects.history');
    Route::get('/projects/pending-approvals', [ProjectController::class, 'pendingApprovals'])
        ->name('projects.pendingApprovals');
    Route::get('/projects/export', [ProjectController::class, 'export'])
        ->name('projects.export');
    Route::post('/projects/import', [ProjectController::class, 'import'])
        ->name('projects.import');
    Route::resource('projects', ProjectController::class)->except(['edit', 'update', 'destroy']);

    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])
        ->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])
        ->name('projects.update');

    Route::post('/projects/{project}/publish', [ProjectController::class, 'publish'])
        ->name('projects.publish');

    Route::post('/projects/{project}/start', [ProjectController::class, 'start'])
        ->name('projects.start');

    Route::post('/projects/{project}/mark-as-done', [ProjectController::class, 'markAsDone'])
        ->name('projects.markAsDone');

    Route::post('/projects/{project}/cancel-submission', [ProjectController::class, 'cancelSubmission'])
        ->name('projects.cancelSubmission');

    Route::post('/projects/{project}/cancel-review-submission', [ProjectController::class, 'cancelReviewSubmission'])
        ->name('projects.cancelReviewSubmission');

    Route::post('/projects/{project}/upload-result', [ProjectController::class, 'uploadResult'])
        ->name('projects.uploadResult');

    Route::patch('/projects/{project}/assign-reviewer', [ProjectController::class, 'assignReviewer'])
        ->name('projects.assignReviewer');

    Route::get('/attachments/{attachment}/download', [ProjectController::class, 'downloadAttachment'])
        ->name('attachments.download');

    Route::delete('/attachments/{attachment}', [ProjectController::class, 'destroyAttachment'])
        ->name('attachments.destroy');

    // AuTicket Reviews
    Route::get('/projects/{project}/review', [ReviewController::class, 'create'])
        ->name('reviews.create');

    Route::post('/projects/{project}/review', [ReviewController::class, 'store'])
        ->name('reviews.store');

    Route::get('/projects/{project}/review/edit', [ReviewController::class, 'edit'])
        ->name('reviews.edit');

    Route::put('/projects/{project}/review', [ReviewController::class, 'update'])
        ->name('reviews.update');

    // AuTicket API
    Route::get('/api/auditors', [AuditorController::class, 'getAvailableAuditors'])
        ->name('api.auditors');

    // Auditor Performance Dashboard
    Route::middleware('can:viewAny,App\Models\User')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Master Data Management (Admin only)
    Route::middleware('can:viewAny,App\Models\User')->group(function () {
        Route::resource('departments', \App\Http\Controllers\DepartmentController::class);
        Route::resource('assignment-types', \App\Http\Controllers\AssignmentTypeController::class);
    });

    Route::get('/auditors/export', [AuditorController::class, 'export'])
        ->name('auditors.export');

    Route::get('/auditors', [AuditorController::class, 'index'])
        ->name('auditors.index');

    Route::get('/auditors/{auditor}', [AuditorController::class, 'show'])
        ->name('auditors.show');

    Route::get('/auditors/{auditor}/chart-data', [AuditorController::class, 'chartData'])
        ->name('auditors.chart-data');

    // Debug route
    // Route::get('/debug-auditors', function() {
    //     $auditors = \App\Models\Auditor::with('user')->get();

    //     return view('debug.auditors', compact('auditors'));
    // })->name('debug.auditors');
});

require __DIR__ . '/auth.php';
