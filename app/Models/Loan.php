<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'fine_amount',
        'payment_status'
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Hitung denda otomatis
    public function calculateFine()
    {
        if ($this->return_date && $this->due_date) {
            $return = Carbon::parse($this->return_date);
            $due = Carbon::parse($this->due_date);
            if ($return->greaterThan($due)) {
                $daysLate = $return->diffInDays($due);
                return $daysLate * 1000; // Rp 1.000 per hari
            }
        }

        return 0;
    }

    // Cek apakah terlambat
    public function isLate()
    {
        if ($this->status === 'dikembalikan' && $this->return_date) {
            return $this->return_date > $this->due_date;
        }
        return Carbon::now() > $this->due_date && $this->status === 'dipinjam';
    }

    // Update status otomatis
    public function updateStatus()
    {
        if ($this->status === 'dikembalikan') {
            return;
        }

        if ($this->isLate()) {
            $this->status = 'terlambat';
            $this->fine_amount = $this->calculateFine();
        } else {
            $this->status = 'dipinjam';
            $this->fine_amount = 0;
        }

        $this->save();
    }

    // Sisa hari
    public function getDaysRemaining()
    {
        if ($this->status === 'dikembalikan') {
            return 0;
        }
        return Carbon::now()->diffInDays($this->due_date, false);
    }
}