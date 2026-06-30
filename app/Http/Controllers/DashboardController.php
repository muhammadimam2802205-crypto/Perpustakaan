<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Data untuk admin
        if ($user->role === 'admin') {
            $totalBooks = Book::count();
            $totalMembers = Member::count();
            $totalLoans = Loan::count();
            $totalTransactions = Transaction::count();
            $pendingLoans = Loan::where('status', 'pending')->count();
            $overdueLoans = Loan::where('return_date', '<', now())
                                ->where('status', 'borrowed')
                                ->count();
            
            return view('dashboard', compact(
                'user',
                'totalBooks',
                'totalMembers',
                'totalLoans',
                'totalTransactions',
                'pendingLoans',
                'overdueLoans'
            ));
        } 
        
        // Data untuk member - gunakan user_id
        else {
            // Ambil data peminjaman member
            $totalBorrowed = Loan::where('user_id', $user->id)
                                 ->where('status', 'borrowed')
                                 ->count();
            
            $totalReturned = Loan::where('user_id', $user->id)
                                 ->where('status', 'returned')
                                 ->count();
            
            $totalPending = Loan::where('user_id', $user->id)
                                ->where('status', 'pending')
                                ->count();
            
            $overdueLoans = Loan::where('user_id', $user->id)
                                ->where('return_date', '<', now())
                                ->where('status', 'borrowed')
                                ->count();
            
            $recentLoans = Loan::where('user_id', $user->id)
                               ->with('book')
                               ->orderBy('created_at', 'desc')
                               ->limit(5)
                               ->get();
            
            return view('member.dashboard', compact(
                'user',
                'totalBorrowed',
                'totalReturned',
                'totalPending',
                'overdueLoans',
                'recentLoans'
            ));
        }
    }
}