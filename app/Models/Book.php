<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_buku',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'kategori_id',
        'cover',
        'deskripsi',
        'stok',
        'available_stock' // Tambahkan available_stock
    ];

    // Relasi ke Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    // Status otomatis
    public function getStatusAttribute()
    {
        return $this->available_stock > 0 ? 'Tersedia' : 'Dipinjam';
    }

    public function isAvailable()
    {
        return $this->available_stock > 0;
    }

    // Decrement stock saat dipinjam
    public function decrementStock()
    {
        $this->decrement('available_stock');
        if ($this->available_stock < 0) {
            $this->update(['available_stock' => 0]);
        }
    }

    // Increment stock saat dikembalikan
    public function incrementStock()
    {
        $this->increment('available_stock');
        if ($this->available_stock > $this->stok) {
            $this->update(['available_stock' => $this->stok]);
        }
    }
}