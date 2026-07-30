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

            $loans = Loan::with(['user', 'book'])
                ->where(function ($query) {
                    $query->where('fine_amount', '>', 0)
                          ->orWhere('status', 'terlambat');
                })
                ->where(function ($query) {
                    $query->whereNull('payment_status')
                          ->orWhere('payment_status', 'belum_bayar');
                })
                ->paginate(10);

        } else {

            $loans = Loan::with(['book'])
                ->where('user_id', $user->id)
                ->where(function ($query) {
                    $query->where('fine_amount', '>', 0)
                          ->orWhere('status', 'terlambat');
                })
                ->where(function ($query) {
                    $query->whereNull('payment_status')
                          ->orWhere('payment_status', 'belum_bayar');
                })
                ->paginate(10);
        }

        foreach ($loans as $loan) {
            $loan->updateStatus();
        }

        return view('denda.index', compact('loans'));
    }

    public function payment($id)
    {
        $loan = Loan::with(['user', 'book'])->findOrFail($id);

        if ($loan->user_id != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('denda.payment', compact('loan'));
    }

    public function confirmPayment($id)
    {
        $loan = Loan::findOrFail($id);

        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $loan->payment_status = 'lunas';
        $loan->save();

        return redirect()
            ->route('denda.index')
            ->with('success', 'Pembayaran denda berhasil dikonfirmasi');
    }
}