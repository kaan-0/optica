<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login'); // or return view('welcome');
});

// Auth routes (login, register, forgot password, etc.)
Auth::routes();

// Home page after login
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Protected routes (only accessible when logged in)
Route::middleware(['auth'])->group(function () {
    // Route::resource('roles', RoleController::class);
    // Route::resource('users', UserController::class);
    Route::get('products/search', [App\Http\Controllers\ProductController::class, 'search'])->name('products.search');
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::resource('products', ProductController::class);
    Route::resource('patients', PatientController::class);

    

    
});

Route::middleware(['role:Admin'])->group(function () {
        Route::resource('users', App\Http\Controllers\UserController::class);
        Route::resource('roles', App\Http\Controllers\RoleController::class);
    });

    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/cancel', [App\Http\Controllers\InvoiceController::class, 'cancel'])->name('invoices.cancel');

    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    