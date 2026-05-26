<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    // Menentukan nama tabel (optional jika nama tabel sudah jamak/plural)
    protected $table = 'produks';

    // Kolom yang boleh diisi lewat form
  protected $fillable = [
    'user_id',
    'nama_produk',
    'harga',
    'jumlah',
    'deskripsi',
    'gambar',
    'gambar_public_id', // ← tambahkan ini
    'status'
];

    // Relasi balik ke User (Pemilik Produk)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Di App\Models\Produk.php
public function mitra()
{
    return $this->belongsTo(\App\Models\Mitra::class, 'mitra_id');
}
}
