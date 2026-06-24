<?php
namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Jika member, tampilkan dashboard member
        if ($user->isMember()) {
            return $this->memberDashboard();
        }

        // Dashboard Admin
        $totalBooks = Book::count();
        $totalMembers = User::where('role', 'member')->count();
        
        // Transaksi peminjaman (menggunakan tabel loans)
        $totalTransactions = Loan::where('status', 'dipinjam')->count();
        $totalReturned = Loan::where('status', 'dikembalikan')->count();
        $totalAvailable = Book::sum('available_stock');
        $totalOverdue = Loan::where('status', 'terlambat')->count();
        $totalUnpaid = Loan::where('payment_status', 'belum_bayar')->sum('fine_amount');

        // Chart data - Peminjaman per bulan
        $monthlyLoans = Loan::select(
            DB::raw('MONTH(borrow_date) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('borrow_date', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $labels = [];
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->format('M');
            $data[] = $monthlyLoans->firstWhere('month', $i)->total ?? 0;
        }

        // Buku terpopuler
        $popularBooks = Book::withCount('loans')
                            ->orderBy('loans_count', 'desc')
                            ->limit(5)
                            ->get();

        // Member teraktif
        $activeMembers = User::withCount('loans')
                            ->where('role', 'member')
                            ->orderBy('loans_count', 'desc')
                            ->limit(5)
                            ->get();

        return view('dashboard', compact(
            'totalBooks',
            'totalMembers',
            'totalTransactions',
            'totalReturned',
            'totalAvailable',
            'totalOverdue',
            'totalUnpaid',
            'labels',
            'data',
            'popularBooks',
            'activeMembers'
        ));
    }

    private function memberDashboard()
    {
        $user = auth()->user();
        
        $activeLoans = Loan::where('user_id', $user->id)
                          ->whereIn('status', ['dipinjam', 'terlambat'])
                          ->with(['book'])
                          ->get();

        $historyLoans = Loan::where('user_id', $user->id)
                           ->where('status', 'dikembalikan')
                           ->with(['book'])
                           ->limit(5)
                           ->get();

        $totalFines = Loan::where('user_id', $user->id)
                         ->where('payment_status', 'belum_bayar')
                         ->sum('fine_amount');

        $totalBorrowed = Loan::where('user_id', $user->id)
                            ->whereIn('status', ['dipinjam', 'terlambat'])
                            ->count();

        return view('member.dashboard', compact(
            'activeLoans',
            'historyLoans',
            'totalFines',
            'totalBorrowed'
        ));
    }
}