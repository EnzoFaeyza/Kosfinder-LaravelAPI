<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
    'user_id',
    'kost_id',
    'nama_penyewa',
    'no_hp',
    'email',
    'jenis_kelamin',
    'tanggal_masuk',
    'durasi_sewa',
    'jumlah_penghuni',
    'catatan',
    'total_harga',
    'status',
    'order_id',
    'snap_token',
    'status_pembayaran',
];

    public function kost()
    {
        return $this->belongsTo(Kost::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}