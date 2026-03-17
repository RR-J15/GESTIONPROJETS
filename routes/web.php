<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Clients — admin uniquement
    Route::resource('clients', ClientController::class)
        ->middleware('role:admin');

    // Devis — admin uniquement
    Route::resource('quotes', QuoteController::class)
        ->middleware('role:admin');
    Route::get('quotes/{quote}/pdf', [QuoteController::class, 'exportPdf'])
        ->name('quotes.pdf')
        ->middleware('role:admin');
    Route::post('quotes/{quote}/convert', [QuoteController::class, 'convertToProject'])
        ->name('quotes.convert')
        ->middleware('role:admin');
    Route::patch('quotes/{quote}/status', [QuoteController::class, 'updateStatus'])
        ->name('quotes.status')
        ->middleware('role:admin');

    // Factures — admin uniquement
    Route::get('invoices', [InvoiceController::class, 'index'])
        ->name('invoices.index')
        ->middleware('role:admin');
    Route::get('invoices/create', [InvoiceController::class, 'create'])
        ->name('invoices.create')
        ->middleware('role:admin');
    Route::post('invoices', [InvoiceController::class, 'store'])
        ->name('invoices.store')
        ->middleware('role:admin');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])
        ->name('invoices.show')
        ->middleware('role:admin');
    Route::get('invoices/{invoice}/edit', [InvoiceController::class, 'edit'])
        ->name('invoices.edit')
        ->middleware('role:admin');
    Route::put('invoices/{invoice}', [InvoiceController::class, 'update'])
        ->name('invoices.update')
        ->middleware('role:admin');
    Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])
        ->name('invoices.destroy')
        ->middleware('role:admin');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'exportPdf'])
        ->name('invoices.pdf')
        ->middleware('role:admin');
    Route::patch('invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])
        ->name('invoices.status')
        ->middleware('role:admin');

    // Projets
    Route::get('projects', [ProjectController::class, 'index'])
        ->name('projects.index')
        ->middleware('role:admin,membre');
    Route::get('projects/create', [ProjectController::class, 'create'])
        ->name('projects.create')
        ->middleware('role:admin');
    Route::post('projects', [ProjectController::class, 'store'])
        ->name('projects.store')
        ->middleware('role:admin');
    Route::get('projects/{project}', [ProjectController::class, 'show'])
        ->name('projects.show')
        ->middleware('role:admin,membre');
    Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])
        ->name('projects.edit')
        ->middleware('role:admin');
    Route::put('projects/{project}', [ProjectController::class, 'update'])
        ->name('projects.update')
        ->middleware('role:admin');
    Route::delete('projects/{project}', [ProjectController::class, 'destroy'])
        ->name('projects.destroy')
        ->middleware('role:admin');
    Route::post('projects/{project}/members', [ProjectController::class, 'addMember'])
        ->name('projects.members.add')
        ->middleware('role:admin');
    Route::delete('projects/{project}/members/{user}', [ProjectController::class, 'removeMember'])
        ->name('projects.members.remove')
        ->middleware('role:admin');

    // Tâches
    Route::get('projects/{project}/tasks/create', [TaskController::class, 'create'])
        ->name('projects.tasks.create')
        ->middleware('role:admin');
    Route::post('projects/{project}/tasks', [TaskController::class, 'store'])
        ->name('projects.tasks.store')
        ->middleware('role:admin');
    Route::get('tasks/{task}', [TaskController::class, 'show'])
        ->name('tasks.show')
        ->middleware('role:admin,membre');
    Route::get('tasks/{task}/edit', [TaskController::class, 'edit'])
        ->name('tasks.edit')
        ->middleware('role:admin,membre');
    Route::put('tasks/{task}', [TaskController::class, 'update'])
        ->name('tasks.update')
        ->middleware('role:admin,membre');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])
        ->name('tasks.destroy')
        ->middleware('role:admin');
    Route::post('tasks/{task}/comments', [TaskController::class, 'addComment'])
        ->name('tasks.comments.store')
        ->middleware('role:admin,membre');
    Route::post('tasks/{task}/attachments', [TaskController::class, 'addAttachment'])
        ->name('tasks.attachments.store')
        ->middleware('role:admin,membre');

    // Notifications
    Route::delete('notifications/destroy-all', [NotificationController::class, 'destroyAll'])
        ->name('notifications.destroyAll')
        ->middleware('role:admin,membre');
    Route::get('notifications', [NotificationController::class, 'index'])
        ->name('notifications.index')
        ->middleware('role:admin,membre');
    Route::patch('notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read')
        ->middleware('role:admin,membre');
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Invitations publiques
Route::get('invite/{token}', [InvitationController::class, 'show'])->name('invite.show');
Route::post('invite/{token}', [InvitationController::class, 'accept'])->name('invite.accept');

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::post('invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::delete('invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');
});

require __DIR__.'/auth.php';
