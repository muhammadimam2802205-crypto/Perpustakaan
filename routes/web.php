<<<<<<< HEAD
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <span class="brand-text font-weight-light">Perpustakaan</span>
    </a>
=======
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
>>>>>>> d9a3b0e92034a948b299d0a6b30054d3ce569d7b

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="{{ route('profile.index') }}" class="d-block">{{ Auth::user()->name }}</a>
                <small class="text-muted">{{ ucfirst(Auth::user()->role) }}</small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

<<<<<<< HEAD
                <!-- Books - All users -->
                <li class="nav-item">
                    <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Buku</p>
                    </a>
                </li>
=======
// Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
>>>>>>> d9a3b0e92034a948b299d0a6b30054d3ce569d7b

                <!-- Admin Only -->
                @if(Auth::user()->role == 'admin')
                <li class="nav-header">MANAJEMEN</li>
                
                <li class="nav-item">
                    <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Kategori</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('members.index') }}" class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Anggota</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('loans.index') }}" class="nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-hand-holding"></i>
                        <p>Peminjaman</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>Transaksi</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('denda.index') }}" class="nav-link {{ request()->routeIs('denda.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Denda</p>
                    </a>
                </li>
                @endif

                <!-- Member Only -->
                @if(Auth::user()->role == 'member')
                <li class="nav-header">AKTIVITAS</li>
                
                <li class="nav-item">
                    <a href="{{ route('loans.index') }}" class="nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-hand-holding"></i>
                        <p>Peminjaman Saya</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('denda.index') }}" class="nav-link {{ request()->routeIs('denda.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Denda Saya</p>
                    </a>
                </li>
                @endif

                <!-- Profile -->
                <li class="nav-header">AKUN</li>
                <li class="nav-item">
                    <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profil</p>
                    </a>
                </li>

<<<<<<< HEAD
                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                    <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
=======
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
>>>>>>> d9a3b0e92034a948b299d0a6b30054d3ce569d7b
