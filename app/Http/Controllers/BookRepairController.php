<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookRepairController extends Controller
{
    //
}
<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookRepair;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookRepairController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $repairs = BookRepair::with(['book', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $repairs = BookRepair::with(['book'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        // Update status otomatis
        foreach ($repairs as $repair) {
            $repair->updateStatus();
        }

        return view('repairs.index', compact('repairs'));
    }

    public function create()
    {
        $books = Book::where('is_under_repair', false)->get();
        return view('repairs.create', compact('books'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'repair_date' => 'required|date|after_or_equal:today',
            'description' => 'required|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        $book = Book::findOrFail($request->book_id);

        // Cek apakah buku sedang dalam perbaikan
        if ($book->is_under_repair) {
            return back()->with('error', 'Buku ini sedang dalam perbaikan.');
        }

        $repairDate = Carbon::parse($request->repair_date);
        $deadlineDate = $repairDate->copy()->addDays(7);

        $repair = BookRepair::create([
            'book_id' => $request->book_id,
            'user_id' => Auth::id(),
            'description' => $request->description,
            'repair_date' => $repairDate,
            'deadline_date' => $deadlineDate,
            'status' => 'menunggu',
            'fine_amount' => 0,
            'payment_status' => null,
            'notes' => $request->notes,
        ]);

        // Update status buku
        $book->is_under_repair = true;
        $book->repair_status = 'perbaikan';
        $book->save();

        return redirect()->route('repairs.index')
            ->with('success', 'Perbaikan buku berhasil ditambahkan! Batas waktu: ' . $deadlineDate->format('d/m/Y'));
    }

    public function show($id)
    {
        $repair = BookRepair::with(['book', 'user'])->findOrFail($id);

        if ($repair->user_id != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }

        $repair->updateStatus();

        return view('repairs.show', compact('repair'));
    }

    public function edit($id)
    {
        $repair = BookRepair::findOrFail($id);

        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya admin yang bisa mengedit data perbaikan');
        }

        $books = Book::all();
        return view('repairs.edit', compact('repair', 'books'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya admin yang bisa mengedit data perbaikan');
        }

        $repair = BookRepair::findOrFail($id);

        $request->validate([
            'repair_date' => 'required|date',
            'description' => 'required|string|max:500',
            'status' => 'required|in:menunggu,proses,selesai,terlambat',
            'notes' => 'nullable|string|max:500',
        ]);

        $repairDate = Carbon::parse($request->repair_date);
        $deadlineDate = $repairDate->copy()->addDays(7);

        $repair->repair_date = $repairDate;
        $repair->deadline_date = $deadlineDate;
        $repair->description = $request->description;
        $repair->status = $request->status;
        $repair->notes = $request->notes;

        // Jika status selesai, set completion_date
        if ($request->status === 'selesai' && !$repair->completion_date) {
            $repair->completion_date = Carbon::now();
            $repair->fine_amount = $repair->calculateFine();

            if ($repair->fine_amount > 0) {
                $repair->payment_status = 'belum_bayar';
            }

            // Update status buku
            $book = $repair->book;
            $book->is_under_repair = false;
            $book->repair_status = 'baik';
            $book->save();
        }

        $repair->save();

        return redirect()->route('repairs.show', $repair->id)
            ->with('success', 'Data perbaikan berhasil diperbarui!');
    }

    // ==============================================================
    // UPDATE DENDA MANUAL (ADMIN ONLY)
    // ==============================================================
    public function updateFine(Request $request, $id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya admin yang bisa mengubah denda.');
        }

        $repair = BookRepair::findOrFail($id);

        $request->validate([
            'fine_amount' => 'required|integer|min:0',
        ]);

        $repair->fine_amount = $request->fine_amount;

        if ($repair->fine_amount > 0) {
            $repair->payment_status = 'belum_bayar';
        } else {
            $repair->payment_status = null;
        }

        $repair->save();

        return redirect()->back()->with('success', '💸 Denda perbaikan berhasil diperbarui!');
    }

    // ==============================================================
    // KONFIRMASI SELESAI PERBAIKAN
    // ==============================================================
    public function complete($id)
    {
        $repair = BookRepair::findOrFail($id);

        if ($repair->user_id != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($repair->status === 'selesai') {
            return back()->with('error', 'Perbaikan ini sudah selesai.');
        }

        $repair->completion_date = Carbon::now();
        $repair->status = 'selesai';
        $repair->fine_amount = $repair->calculateFine();

        if ($repair->fine_amount > 0) {
            $repair->payment_status = 'belum_bayar';
        }

        $repair->save();

        // Update status buku
        $book = $repair->book;
        $book->is_under_repair = false;
        $book->repair_status = 'baik';
        $book->save();

        $message = '✅ Perbaikan selesai!';
        if ($repair->fine_amount > 0) {
            $message .= ' 💰 Denda: Rp ' . number_format($repair->fine_amount, 0, ',', '.');
        }

        return redirect()->route('repairs.index')->with('success', $message);
    }

    // ==============================================================
    // HAPUS DATA PERBAIKAN (ADMIN ONLY)
    // ==============================================================
    public function destroy($id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $repair = BookRepair::findOrFail($id);

        // Kembalikan status buku
        if ($repair->status !== 'selesai') {
            $book = $repair->book;
            $book->is_under_repair = false;
            $book->repair_status = 'baik';
            $book->save();
        }

        $repair->delete();

        return redirect()->route('repairs.index')
            ->with('success', 'Data perbaikan berhasil dihapus.');
    }
}