<?php

use App\Http\Controllers\AuditingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PropertyAsbestosAuditController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Login/Index')->name('login');

Route::inertia('/dashboard', 'Dashboard/Index')->name('dashboard');
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
Route::get('/asbestos-audits', [PropertyAsbestosAuditController::class, 'index'])->name('asbestosaudits.index');
Route::get('/asbestos-audits/create', [PropertyAsbestosAuditController::class, 'create'])->name('asbestosaudits.create');
Route::post('/asbestos-audits', [PropertyAsbestosAuditController::class, 'store'])->name('asbestosaudits.store');
Route::get('/asbestos-audits/{asbestosaudit}', [PropertyAsbestosAuditController::class, 'show'])->name('asbestosaudits.show');
Route::get('/asbestos-audits/{asbestosaudit}/edit', [PropertyAsbestosAuditController::class, 'edit'])->name('asbestosaudits.edit');
Route::put('/asbestos-audits/{asbestosaudit}', [PropertyAsbestosAuditController::class, 'update'])->name('asbestosaudits.update');
Route::delete('/asbestos-audits/{asbestosaudit}', [PropertyAsbestosAuditController::class, 'destroy'])->name('asbestosaudits.destroy');

Route::get('/auditing/{asbestosaudit}', [AuditingController::class, 'show'])->name('auditing.show');

// Route::get('/error/403', fn () => abort(403));
// Route::get('/error/500', fn () => abort(500));
