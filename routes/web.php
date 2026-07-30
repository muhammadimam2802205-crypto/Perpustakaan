<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\DendaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BookRepairController; // ← TAMBAHKAN INI
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect()->route('login');
});

// ============ AUTH ROUTES ============
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/verify-otp', [RegisterController::class, 'showOtpForm'])->name('otp.verify.form');
Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/resend-otp', [RegisterController::class, 'resendOtp'])->name('otp.resend');

// Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (auth()->attempt($credentials, $request->remember)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
});

Route::post('/logout', function (Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// ============ PROTECTED ROUTES ============
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Books - Semua user bisa lihat
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/search-api', [BookController::class, 'searchApi'])->name('books.search-api');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    
    // Book CRUD - HANYA ADMIN
    Route::middleware(['check.role:admin'])->group(function () {
        Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
        Route::post('/books', [BookController::class, 'store'])->name('books.store');
        Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
        Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
        Route::get('/books/import', [BookController::class, 'importForm'])->name('books.import');
        Route::post('/books/import', [BookController::class, 'importFromApi'])->name('books.import.store');
    });

    // Categories - Admin only
    Route::middleware(['check.role:admin'])->group(function () {
        Route::resource('categories', CategoryController::class);
    });

    // Members - Admin only
    Route::middleware(['check.role:admin'])->group(function () {
        Route::resource('members', MemberController::class);
    });

    // ==============================================================
    // ============ LOANS ROUTES =====================================
    // ==============================================================
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    Route::get('/loans/{id}', [LoanController::class, 'show'])->name('loans.show');
    Route::post('/loans/{id}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    
    // UPDATE DENDA (ADMIN ONLY)
    Route::put('/loans/{id}/update-fine', [LoanController::class, 'updateFine'])
        ->name('loans.updateFine')
        ->middleware(['check.role:admin']);
    
    // Hapus loan (ADMIN ONLY)
    Route::middleware(['check.role:admin'])->group(function () {
        Route::delete('/loans/{id}', [LoanController::class, 'destroy'])->name('loans.destroy');
    });

    // ==============================================================
    // ============ DENDA ROUTES =====================================
    // ==============================================================
    Route::get('/denda', [DendaController::class, 'index'])->name('denda.index');
    Route::get('/denda/{id}/payment', [DendaController::class, 'payment'])->name('denda.payment');
    Route::post('/denda/{id}/confirm', [DendaController::class, 'confirmPayment'])->name('denda.confirm');

    // ==============================================================
    // ============ BOOK REPAIRS ROUTES ==============================
    // ==============================================================
    Route::get('/repairs', [BookRepairController::class, 'index'])->name('repairs.index');
    Route::get('/repairs/create', [BookRepairController::class, 'create'])->name('repairs.create');
    Route::post('/repairs', [BookRepairController::class, 'store'])->name('repairs.store');
    Route::get('/repairs/{id}', [BookRepairController::class, 'show'])->name('repairs.show');
    
    // Selesaikan perbaikan (Member & Admin)
    Route::post('/repairs/{id}/complete', [BookRepairController::class, 'complete'])->name('repairs.complete');
    
    // ADMIN ONLY - Kelola Perbaikan
    Route::middleware(['check.role:admin'])->group(function () {
        Route::get('/repairs/{id}/edit', [BookRepairController::class, 'edit'])->name('repairs.edit');
        Route::put('/repairs/{id}', [BookRepairController::class, 'update'])->name('repairs.update');
        Route::put('/repairs/{id}/update-fine', [BookRepairController::class, 'updateFine'])->name('repairs.updateFine');
        Route::delete('/repairs/{id}', [BookRepairController::class, 'destroy'])->name('repairs.destroy');
    });

    // Transactions
    Route::resource('transactions', TransactionController::class);
    Route::post('/transactions/{transaction}/return', [TransactionController::class, 'returnBook'])->name('transactions.return');
});