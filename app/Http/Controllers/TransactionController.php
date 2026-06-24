<?php
namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['book', 'member'])
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $books = Book::where('status', 'tersedia')->where('stok', '>', 0)->get();
        $members = Member::where('status', 'aktif')->get();
        return view('transactions.create', compact('books', 'members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'member_id' => 'required|exists:members,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam'
        ]);

        $book = Book::find($request->book_id);
        
        if ($book->stok <= 0) {
            return back()->with('error', 'Stok buku tidak tersedia.');
        }

        $transaction = Transaction::create([
            'kode_transaksi' => 'TRX-' . strtoupper(Str::random(10)),
            'book_id' => $request->book_id,
            'member_id' => $request->member_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'status' => 'dipinjam'
        ]);

        $book->decrement('stok');
        if ($book->stok == 0) {
            $book->update(['status' => 'dipinjam']);
        }

        return redirect()->route('transactions.index')
                        ->with('success', 'Transaksi peminjaman berhasil.');
    }

    public function returnBook(Transaction $transaction)
    {
        if ($transaction->status === 'dikembalikan') {
            return back()->with('error', 'Buku sudah dikembalikan.');
        }

        $denda = $transaction->calculateDenda();
        
        $transaction->update([
            'tanggal_kembali_aktual' => Carbon::now(),
            'status' => $transaction->isLate() ? 'terlambat' : 'dikembalikan',
            'denda' => $denda
        ]);

        $book = $transaction->book;
        $book->increment('stok');
        $book->update(['status' => 'tersedia']);

        return redirect()->route('transactions.index')
                        ->with('success', 'Buku berhasil dikembalikan. Denda: Rp ' . number_format($denda, 0, ',', '.'));
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->status === 'dipinjam') {
            $book = $transaction->book;
            $book->increment('stok');
            $book->update(['status' => 'tersedia']);
        }

        $transaction->delete();

        return redirect()->route('transactions.index')
                        ->with('success', 'Transaksi berhasil dihapus.');
    }
}