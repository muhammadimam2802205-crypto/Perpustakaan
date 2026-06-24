<?php
namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $loans = Loan::with(['user', 'book'])->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $loans = Loan::with(['book'])->where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(10);
        }

        // Update status loans yang terlambat
        foreach ($loans as $loan) {
            $loan->updateStatus();
        }

        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        $books = Book::where('available_stock', '>', 0)->get();
        return view('loans.create', compact('books'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($request->book_id);
        
        if (!$book->isAvailable()) {
            return back()->with('error', 'Buku tidak tersedia');
        }

        // Cek apakah user sudah meminjam buku ini
        $existingLoan = Loan::where('user_id', Auth::id())
                            ->where('book_id', $book->id)
                            ->whereIn('status', ['dipinjam', 'terlambat'])
                            ->first();

        if ($existingLoan) {
            return back()->with('error', 'Anda sudah meminjam buku ini');
        }

        $loan = Loan::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'borrow_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays(7),
            'status' => 'dipinjam',
            'payment_status' => 'belum_bayar'
        ]);

        // Kurangi stock
        $book->decrementStock();

        return redirect()->route('loans.index')
                        ->with('success', 'Peminjaman berhasil! Batas pengembalian: ' . $loan->due_date->format('d/m/Y'));
    }

    public function returnBook($id)
    {
        $loan = Loan::findOrFail($id);
        
        if ($loan->user_id != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($loan->status === 'dikembalikan') {
            return back()->with('error', 'Buku sudah dikembalikan');
        }

        $loan->return_date = Carbon::now();
        $loan->status = 'dikembalikan';
        $loan->fine_amount = $loan->calculateFine();
        
        if ($loan->fine_amount > 0) {
            $loan->payment_status = 'belum_bayar';
        }

        $loan->save();

        // Tambah stock
        $book = $loan->book;
        $book->incrementStock();

        return redirect()->route('loans.index')
                        ->with('success', 'Buku berhasil dikembalikan' . 
                               ($loan->fine_amount > 0 ? '. Denda: Rp ' . number_format($loan->fine_amount, 0, ',', '.') : ''));
    }

    public function show($id)
{
    $loan = Loan::with(['user', 'book'])->findOrFail($id);
    
    if ($loan->user_id != Auth::id() && !Auth::user()->isAdmin()) {
        abort(403, 'Anda tidak memiliki akses ke data ini');
    }

    return view('loans.show', compact('loan'));
}

    public function destroy($id)
    {
        $loan = Loan::findOrFail($id);
        
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($loan->status === 'dipinjam' || $loan->status === 'terlambat') {
            $book = $loan->book;
            $book->incrementStock();
        }

        $loan->delete();

        return redirect()->route('loans.index')
                        ->with('success', 'Data peminjaman berhasil dihapus');
    }
}