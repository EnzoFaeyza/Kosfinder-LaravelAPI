<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Kost;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request)
{
    $kost = Kost::findOrFail($request->kost_id);

    $totalHarga = $kost->harga_per_bulan * $request->durasi_sewa;

    $request->validate([
        'kost_id' => 'required|exists:kosts,id',
        'nama_penyewa' => 'required|string',
        'no_hp' => 'required|string',
        'email' => 'nullable|email',
        'jenis_kelamin' => 'required|in:laki-laki,perempuan',
        'tanggal_masuk' => 'required|date',
        'durasi_sewa' => 'required|integer|in:1,3,12',
        'jumlah_penghuni' => 'required|integer|min:1',
        'catatan' => 'nullable|string',
    ]);
    

    $booking = Booking::create([
    'user_id' => auth()->id(),
    'kost_id' => $request->kost_id,
    'nama_penyewa' => $request->nama_penyewa,
    'no_hp' => $request->no_hp,
    'email' => $request->email,
    'jenis_kelamin' => $request->jenis_kelamin,
    'tanggal_masuk' => $request->tanggal_masuk,
    'durasi_sewa' => $request->durasi_sewa,
    'jumlah_penghuni' => $request->jumlah_penghuni,
    'catatan' => $request->catatan,
    'total_harga' => $totalHarga,
    'status' => 'pending',
]);
if ($booking->total_harga <= 0) {
    return response()->json([
        'message' => 'Total harga tidak valid',
        'total_harga' => $booking->total_harga
    ], 400);
}

    return response()->json([
        'message' => 'Booking berhasil dibuat, lanjutkan pembayaran',
        'booking' => $booking
    ], 201);
    

    
}

    public function myBookings()
    {
        $bookings = Booking::with('kost')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pemesanan berhasil diambil.',
            'data' => $bookings
        ], 200);
    }

    public function ownerBookings()
{
    if (!in_array(auth()->user()->role, ['owner', 'super_admin'])) {
        return response()->json([
            'success' => false,
            'message' => 'Akses ditolak.'
        ], 403);
    }

    $bookings = Booking::with(['kost', 'user'])
        ->whereHas('kost', function ($query) {
            $query->where('owner_id', auth()->id());
        })
        ->latest()
        ->get();

    return response()->json([
        'success' => true,
        'message' => 'Data pemesanan berhasil diambil.',
        'data' => $bookings
    ], 200);
}

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,diterima,ditolak,selesai'
        ]);

        $booking = Booking::with('kost')->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Data pemesanan tidak ditemukan.'
            ], 404);
        }

        if (auth()->user()->role !== 'super_admin' && $booking->kost->owner_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda hanya dapat mengubah status pemesanan kos milik Anda sendiri.'
            ], 403);
        }

        $booking->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status pemesanan berhasil diubah.',
            'data' => $booking
        ], 200);
    }
}