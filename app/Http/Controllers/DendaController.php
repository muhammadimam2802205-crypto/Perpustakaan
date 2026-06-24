<?php
namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DendaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $loans = Loan::where('fine_amount', '>', 0)
                        ->where('payment_status', 'belum_bayar')
                        ->with(['user', 'book'])
                        ->get();
        } else {
            $loans = Loan::where('user_id', $user->id)
                        ->where('fine_amount', '>', 0)
                        ->where('payment_status', 'belum_bayar')
                        ->with(['book'])
                        ->get();
        }

        return view('denda.index', compact('loans'));
    }

    public function payment($id)
    {
        $loan = Loan::with(['user', 'book'])->findOrFail($id);
        
        if ($loan->user_id != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Generate QR Code image URL using Google Chart API as fallback without extra composer package
        $qrData = urlencode(json_encode([
            'loan_id' => $loan->id,
            'member' => $loan->user->name,
            'amount' => $loan->fine_amount,
        ]));

        $qrCodeUrl = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={$qrData}&choe=UTF-8";

        return view('denda.payment', compact('loan', 'qrCodeUrl'));
    }

    public function confirmPayment($id)
    {
        $loan = Loan::findOrFail($id);
        
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $loan->payment_status = 'lunas';
        $loan->save();

        return redirect()->route('denda.index')
                        ->with('success', 'Pembayaran denda berhasil dikonfirmasi');
    }
}