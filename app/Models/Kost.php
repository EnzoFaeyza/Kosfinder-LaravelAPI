<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Review;
use App\Models\Booking;
class Kost extends Model
{
    protected $fillable = [
    'owner_id',
    'nama_kost',
    'alamat',
    'harga_per_bulan',
    'jenis_kost',
    'tersedia',
    'jumlah_kamar',
    'kamar_tersedia',
    'fasilitas',
    'thumbnail',
    'gambar_kamar_mandi',
    'gambar_interior',
    'gambar_depan',
];

protected $casts = [
    'fasilitas' => 'array',
    'tersedia' => 'boolean',
];

public function reviews()
{
    return $this->hasMany(Review::class);
}

public function bookings()
{
    return $this->hasMany(Booking::class);
}
}
