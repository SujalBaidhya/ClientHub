<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\ProjectFileController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\MilestoneController as AdminMilestoneController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\ProjectFileController as AdminFileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvitationController;

Route::middleware('web')->group(function () {
    Route::get('/invitation/{token}', [InvitationController::class, 'show'])->name('invitations.show');
    Route::post('/invitation/{token}', [InvitationController::class, 'update'])->name('invitations.update');
});
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login');


Route::post('/login', [LoginController::class, 'login'])
    ->name('login.process');

Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
Route::get('/projects/{project}/files', [AdminFileController::class, 'create'])->name('admin.files.create');
Route::post('/projects/{project}/files', [AdminFileController::class, 'store'])->name('admin.files.store');
Route::delete('/projects/{project}/files/{file}', [AdminFileController::class, 'destroy'])->name('admin.files.destroy');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::get('/dashboard/{project}', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard.project');
    Route::get('/account', [AccountController::class, 'edit'])
    ->middleware('auth')
    ->name('account.edit');

Route::put('/account', [AccountController::class, 'update'])
    ->middleware('auth')
    ->name('account.update');

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::post('/milestones/{milestone}/complete', [MilestoneController::class, 'complete'])
    ->middleware('auth')
    ->name('milestones.complete');

Route::get('/files/{projectFile}/download', [ProjectFileController::class, 'download'])
    ->middleware('auth')
    ->name('files.download');

Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])
    ->middleware('auth')
    ->name('invoices.download');
    Route::post('/milestones/{milestone}/comments', [CommentController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');
    Route::get('/invoices/{invoice}/pay', [PaymentController::class, 'pay'])
    ->middleware('auth')
    ->name('payment.pay');

Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/failure', [PaymentController::class, 'failure'])->name('payment.failure');

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/projects', [AdminProjectController::class, 'index'])->name('admin.projects.index');
Route::get('/projects/create', [AdminProjectController::class, 'create'])->name('admin.projects.create');
Route::post('/projects', [AdminProjectController::class, 'store'])->name('admin.projects.store');
Route::get('/projects/{project}/edit', [AdminProjectController::class, 'edit'])->name('admin.projects.edit');
Route::put('/projects/{project}', [AdminProjectController::class, 'update'])->name('admin.projects.update');
Route::delete('/projects/{project}', [AdminProjectController::class, 'destroy'])->name('admin.projects.destroy');
    Route::get('/projects/{project}/milestones', [AdminMilestoneController::class, 'create'])->name('admin.milestones.create');
Route::post('/projects/{project}/milestones', [AdminMilestoneController::class, 'store'])->name('admin.milestones.store');
Route::put('/projects/{project}/milestones/{milestone}', [AdminMilestoneController::class, 'update'])->name('admin.milestones.update');
Route::delete('/projects/{project}/milestones/{milestone}', [AdminMilestoneController::class, 'destroy'])->name('admin.milestones.destroy');

   Route::get('/projects/{project}/invoices', [AdminInvoiceController::class, 'create'])->name('admin.invoices.create');
Route::post('/projects/{project}/invoices', [AdminInvoiceController::class, 'store'])->name('admin.invoices.store');
Route::put('/projects/{project}/invoices/{invoice}', [AdminInvoiceController::class, 'update'])->name('admin.invoices.update');
Route::delete('/projects/{project}/invoices/{invoice}', [AdminInvoiceController::class, 'destroy'])->name('admin.invoices.destroy');
 Route::get('/clients', [AdminClientController::class, 'create'])->name('admin.clients.create');
Route::post('/clients', [AdminClientController::class, 'store'])->name('admin.clients.store');
Route::delete('/clients/{client}', [AdminClientController::class, 'destroy'])->name('admin.clients.destroy');

});