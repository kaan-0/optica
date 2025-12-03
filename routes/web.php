<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MedicalRecordController; 

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

    // GET /patients/{patient}/medical_records/create -> Muestra el formulario para añadir una consulta a un paciente
    Route::get('patients/{patient}/medical_records/create', [MedicalRecordController::class, 'create'])
        ->name('medical_records.create');
    
    // POST /patients/{patient}/medical_records -> Almacena la nueva consulta
    Route::post('patients/{patient}/medical_records', [MedicalRecordController::class, 'store'])
        ->name('medical_records.store');
    
    // GET /medical_records/{medical_record} -> Muestra el detalle de una consulta específica
    Route::get('medical_records/{medicalRecord}', [MedicalRecordController::class, 'show'])
        ->name('medical_records.show');
    
    Route::get('medical_records/{medical_record}/edit', [MedicalRecordController::class, 'edit'])->name('medical_records.edit');
    Route::put('medical_records/{medical_record}', [MedicalRecordController::class, 'update'])->name('medical_records.update');
    Route::delete('medical_records/{medical_record}', [MedicalRecordController::class, 'destroy'])->name('medical_records.destroy');
    
});

Route::middleware(['role:Admin'])->group(function () {
        Route::resource('users', App\Http\Controllers\UserController::class);
        Route::resource('roles', App\Http\Controllers\RoleController::class);
    });

    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/cancel', [App\Http\Controllers\InvoiceController::class, 'cancel'])->name('invoices.cancel');

    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');


    