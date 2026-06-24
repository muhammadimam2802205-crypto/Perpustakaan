<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_member',
        'nama',
        'email',
        'no_telepon',
        'alamat',
        'tanggal_daftar',
        'status'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}