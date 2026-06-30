<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'user_id', // atau 'member_id'
        'book_id',
        'loan_date',
        'return_date',
        'status',
        'notes'
    ];

<<<<<<< HEAD
    // Relasi ke User
=======
    // ==============================================================
    // RELATIONSHIPS
    // ==============================================================
>>>>>>> d9a3b0e92034a948b299d0a6b30054d3ce569d7b
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Book
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
<<<<<<< HEAD
=======

    // ==============================================================
    // HITUNG DENDA OTOMATIS
    // Aturan: Telat > 7 hari → Rp 5.000/hari (hari ke-8 dan seterusnya)
    // ==============================================================
    public function calculateFine()
    {
        if ($this->status === 'dikembalikan' && $this->return_date) {
            $return = Carbon::parse($this->return_date)->startOfDay();
            $due = Carbon::parse($this->due_date)->startOfDay();

            if ($return->greaterThan($due)) {
                $hariTelat = (int) $due->diffInDays($return);
                return $hariTelat * 1000;
            }
            return 0;
        }

        if ($this->status === 'dipinjam' || $this->status === 'terlambat') {
            $now = Carbon::now()->startOfDay();
            $due = Carbon::parse($this->due_date)->startOfDay();

            if ($now->greaterThan($due)) {
                $hariTelat = (int) $due->diffInDays($now);
                return $hariTelat * 1000;
            }
        }

        return 0;
    }

    // ==============================================================
    // CEK APAKAH TERLAMBAT
    // ==============================================================
    public function isLate()
    {
        if ($this->status === 'dikembalikan' && $this->return_date) {
            return Carbon::parse($this->return_date)->greaterThan($this->due_date);
        }
        
        // Masih dipinjam
        return Carbon::now()->greaterThan($this->due_date) && 
               ($this->status === 'dipinjam' || $this->status === 'terlambat');
    }

    // ==============================================================
    // UPDATE STATUS OTOMATIS
    // ==============================================================
    public function updateStatus()
    {
        if ($this->status === 'dikembalikan') {
            return;
        }

        if ($this->isLate()) {
            $this->status = 'terlambat';
            $calculatedFine = $this->calculateFine();
            $this->fine_amount = $calculatedFine > 0 ? $calculatedFine : 0;
            if ($this->fine_amount > 0) {
                $this->payment_status = $this->payment_status === 'lunas' ? 'lunas' : 'belum_bayar';
            } else {
                $this->payment_status = null;
            }
        } else {
            $this->status = 'dipinjam';
            $this->fine_amount = 0;
            $this->payment_status = null;
        }

        $this->save();
    }

    // ==============================================================
    // HITUNG HARI TELAT
    // ==============================================================
    public function getDaysLate()
    {
        if ($this->status === 'dikembalikan' && $this->return_date) {
            $return = Carbon::parse($this->return_date)->startOfDay();
            $due = Carbon::parse($this->due_date)->startOfDay();
            if ($return->greaterThan($due)) {
                return $due->diffInDays($return);
            }
            return 0;
        }

        if ($this->status === 'dipinjam' || $this->status === 'terlambat') {
            $now = Carbon::now()->startOfDay();
            $due = Carbon::parse($this->due_date)->startOfDay();
            if ($now->greaterThan($due)) {
                return $due->diffInDays($now);
            }
            return 0;
        }

        return 0;
    }

    // ==============================================================
    // SISA HARI SEBELUM JATUH TEMPO
    // ==============================================================
    public function getDaysRemaining()
    {
        if ($this->status === 'dikembalikan') {
            return 0;
        }

        $now = Carbon::now()->startOfDay();
        $due = Carbon::parse($this->due_date)->startOfDay();

        if ($now->greaterThan($due)) {
            return -$due->diffInDays($now);
        }

        return $now->diffInDays($due);
    }

    // ==============================================================
    // CEK APAKAH KENA DENDA (fine_amount > 0)
    // ==============================================================
    public function hasFine()
    {
        return $this->fine_amount > 0;
    }

    // ==============================================================
    // CEK APAKAH DENDA SUDAH LUNAS
    // ==============================================================
    public function isFinePaid()
    {
        return $this->payment_status === 'lunas';
    }

    // ==============================================================
    // SCOPE / QUERY HELPER
    // ==============================================================
    
    // Peminjaman yang aktif (belum dikembalikan)
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['dipinjam', 'terlambat']);
    }

    // Peminjaman yang terlambat
    public function scopeOverdue($query)
    {
        return $query->where('status', 'terlambat')
                     ->orWhere(function($q) {
                         $q->where('status', 'dipinjam')
                           ->where('due_date', '<', Carbon::now());
                     });
    }

    // Peminjaman yang memiliki denda
    public function scopeWithFine($query)
    {
        return $query->where('fine_amount', '>', 0);
    }

    // Peminjaman yang denda belum dibayar
    public function scopeUnpaidFine($query)
    {
        return $query->where('fine_amount', '>', 0)
                     ->where('payment_status', 'belum_bayar');
    }

    // ==============================================================
    // FORMATTER
    // ==============================================================
    public function getFormattedFineAttribute()
    {
        return 'Rp ' . number_format($this->fine_amount, 0, ',', '.');
    }
>>>>>>> d9a3b0e92034a948b299d0a6b30054d3ce569d7b
}