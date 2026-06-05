<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index($kost_id)
    {
        $reviews = Review::with('user:id,name')
            ->where('kost_id', $kost_id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Review kos berhasil diambil.',
            'data' => $reviews
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kost_id' => 'required|exists:kosts,id',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string'
        ]);

        $sudahReview = Review::where('kost_id', $request->kost_id)
            ->where('user_id', auth()->id())
            ->first();

        if ($sudahReview) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memberikan review untuk kos ini.'
            ], 409);
        }

        $review = Review::create([
            'kost_id' => $request->kost_id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review berhasil ditambahkan.',
            'data' => $review
        ], 201);
    }

    public function destroy($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review tidak ditemukan.'
            ], 404);
        }

        if (auth()->user()->role !== 'super_admin' && $review->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda hanya dapat menghapus review milik Anda sendiri.'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review berhasil dihapus.'
        ], 200);
    }
}