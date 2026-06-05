<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createSnapToken($bookingId)
    {
        $booking = Booking::with(['kost', 'user'])->findOrFail($bookingId);

        $orderId = 'BOOKING-' . $booking->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $booking->total_harga,
            ],

            'customer_details' => [
                'first_name' => $booking->nama_penyewa,
                'email' => $booking->email,
                'phone' => $booking->no_hp,
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        $booking->update([
            'order_id' => $orderId,
            'snap_token' => $snapToken,
            'status_pembayaran' => 'Belum Bayar',
        ]);

        return response()->json([
            'snap_token' => $snapToken,
            'order_id' => $orderId
        ]);
    }

    public function notification(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');

        $signature = hash(
            'sha512',
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            $serverKey
        );

        if ($signature !== $request->signature_key) {
            return response()->json([
                'message' => 'Invalid signature'
            ], 403);
        }

        $booking = Booking::where(
            'order_id',
            $request->order_id
        )->first();

        if (!$booking) {
            return response()->json([
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }

        if (
            $request->transaction_status == 'capture' ||
            $request->transaction_status == 'settlement'
        ) {
            $booking->update([
                'status_pembayaran' => 'Sudah Bayar',
                'status' => 'diterima'
            ]);
        }

        if (
            $request->transaction_status == 'deny' ||
            $request->transaction_status == 'expire' ||
            $request->transaction_status == 'cancel'
        ) {
            $booking->update([
                'status_pembayaran' => 'failed'
            ]);
        }

        return response()->json([
            'message' => 'OK'
        ]);
    }
}