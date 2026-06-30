<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookRepairController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DendaController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/verify-otp', [OtpVerificationController::class, 'showForm'])->name('verify.otp.form');
    Route::post('/verify-otp', [OtpVerificationController::class, 'verify'])->name('verify.otp');
    Route::post('/resend-otp', [OtpVerificationController::class, 'resendOtp'])->name('resend.otp');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('books', BookController::class);
    Route::get('/books/import', [BookController::class, 'importForm'])->name('books.import');
    Route::post('/books/import', [BookController::class, 'importFromApi'])->name('books.import.store');
    Route::post('/books/{book}/remove-cover', [BookController::class, 'removeCover'])->name('books.remove-cover');

    Route::middleware(['check.role:admin'])->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('members', MemberController::class);
        Route::resource('transactions', TransactionController::class);
        Route::post('/transactions/{transaction}/return', [TransactionController::class, 'returnBook'])->name('transactions.return');
    });

    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    Route::get('/loans/{id}', [LoanController::class, 'show'])->name('loans.show');
    Route::post('/loans/{id}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    Route::put('/loans/{id}/update-fine', [LoanController::class, 'updateFine'])->name('loans.updateFine');

    Route::middleware(['check.role:admin'])->group(function () {
        Route::delete('/loans/{id}', [LoanController::class, 'destroy'])->name('loans.destroy');
    });

    Route::get('/denda', [DendaController::class, 'index'])->name('denda.index');
    Route::get('/denda/{id}/payment', [DendaController::class, 'payment'])->name('denda.payment');
    Route::post('/denda/{id}/confirm', [DendaController::class, 'confirmPayment'])->name('denda.confirm');

    Route::get('/repairs', [BookRepairController::class, 'index'])->name('repairs.index');
    Route::get('/repairs/create', [BookRepairController::class, 'create'])->name('repairs.create');
    Route::post('/repairs', [BookRepairController::class, 'store'])->name('repairs.store');
    Route::get('/repairs/{id}', [BookRepairController::class, 'show'])->name('repairs.show');
    Route::post('/repairs/{id}/complete', [BookRepairController::class, 'complete'])->name('repairs.complete');

    Route::middleware(['check.role:admin'])->group(function () {
        Route::get('/repairs/{id}/edit', [BookRepairController::class, 'edit'])->name('repairs.edit');
        Route::put('/repairs/{id}', [BookRepairController::class, 'update'])->name('repairs.update');
        Route::put('/repairs/{id}/update-fine', [BookRepairController::class, 'updateFine'])->name('repairs.updateFine');
        Route::delete('/repairs/{id}', [BookRepairController::class, 'destroy'])->name('repairs.destroy');
    });
});
