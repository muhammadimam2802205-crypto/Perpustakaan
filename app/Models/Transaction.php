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
        if ($this->status === 'dikembalikan') {
            return $this->tanggal_kembali_aktual > $this->tanggal_kembali;
        }
        return Carbon::now() > $this->tanggal_kembali;
    }

    public function calculateDenda()
    {
        if ($this->status === 'dikembalikan' && $this->isLate()) {
            $daysLate = Carbon::parse($this->tanggal_kembali_aktual)->diffInDays($this->tanggal_kembali);
            return $daysLate * 5000;
        }
        return 0;
    }
}