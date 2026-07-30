<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_transaksi',
        'book_id',
        'member_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'tanggal_kembali_aktual',
        'denda',
        'status'
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
        'tanggal_kembali_aktual' => 'date',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function isLate()
    {
        $actual = null;
        if ($this->status === 'dikembalikan' && $this->tanggal_kembali_aktual) {
            $actual = Carbon::parse($this->tanggal_kembali_aktual)->startOfDay();
        } else {
            $actual = Carbon::now()->startOfDay();
        }
        $due = Carbon::parse($this->tanggal_kembali)->startOfDay();
        return $actual->greaterThan($due);
    }

    public function calculateDenda()
    {
        $actualReturn = null;
        if ($this->status === 'dikembalikan' && $this->tanggal_kembali_aktual) {
            $actualReturn = Carbon::parse($this->tanggal_kembali_aktual)->startOfDay();
        } else {
            $actualReturn = Carbon::now()->startOfDay();
        }

        $due = Carbon::parse($this->tanggal_kembali)->startOfDay();

        if ($actualReturn->greaterThan($due)) {
            $daysLate = (int) $due->diffInDays($actualReturn);
            return $daysLate * 1000;
        }
        return 0;
    }
}