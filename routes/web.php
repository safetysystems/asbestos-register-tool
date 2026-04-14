<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Login/Index')->name('login');

Route::inertia('/dashboard', 'Dashboard/Index')->name('dashboard');
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
Route::inertia('/asbestos-audits', 'AsbestosAudits/Index')->name('asbestosaudits');
Route::inertia('/asbestos-audits/create', 'AsbestosAudits/CreateForm')->name('asbestosaudits.createform');
Route::inertia('/asbestos-audits/edit', 'AsbestosAudits/EditForm')->name('asbestosaudits.editform');
