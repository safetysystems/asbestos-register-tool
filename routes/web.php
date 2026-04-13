<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Login/Index')->name('login');

Route::inertia('/dashboard', 'Dashboard/Index')->name('dashboard');
Route::inertia('/customers', 'Customers/Index')->name('customers');
Route::inertia('/asbestos-audits', 'AsbestosAudits/Index')->name('asbestosaudits');
Route::inertia('/asbestos-audits/create', 'AsbestosAudits/CreateForm')->name('asbestosaudits.createform');
Route::inertia('/asbestos-audits/edit', 'AsbestosAudits/EditForm')->name('asbestosaudits.editform');