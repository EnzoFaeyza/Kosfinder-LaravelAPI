<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kost;
use Illuminate\Http\Request;

class KostController extends Controller
{
    public function index(Request $request)
    {
        $query = Kost::query();

        if ($request->has('jenis_kost')) {
            $query->where('jenis_kost', $request->jenis_kost);
        }

        if ($request->has('min_harga')) {
            $query->where('harga_per_bulan', '>=', $request->min_harga);
        }

        if ($request->has('max_harga')) {
            $query->where('harga_per_bulan', '<=', $request->max_harga);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data kos berhasil diambil.',
            'data' => $query->get()
        ], 200);
    }

    public function show($id)
    {
        $kost = Kost::find($id);

        if (!$kost) {
            return response()->json([
                'success' => false,
                'message' => 'Data kos tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kos berhasil diambil.',
            'data' => $kost
        ], 200);
    }

    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['owner', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya pemilik kos (owner) atau administrator yang dapat menambahkan data kos.'
            ], 403);
        }

        $request->validate([
            'nama_kost' => 'required|string|max:255',
            'alamat' => 'required|string',
            'harga_per_bulan' => 'required|integer|min:0',
            'jenis_kost' => 'required|in:putra,putri,campur',
            'jumlah_kamar' => 'required|integer|min:1',
            'kamar_tersedia' => 'required|integer|min:0|lte:jumlah_kamar',
            'fasilitas' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'gambar_kamar_mandi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'gambar_interior' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'gambar_depan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $thumbnail = $request->file('thumbnail')?->store('kosts', 'public');
        $gambarKamarMandi = $request->file('gambar_kamar_mandi')?->store('kosts', 'public');
        $gambarInterior = $request->file('gambar_interior')?->store('kosts', 'public');
        $gambarDepan = $request->file('gambar_depan')?->store('kosts', 'public');

        $kost = Kost::create([
    'owner_id' => auth()->id(),
    'nama_kost' => $request->nama_kost,
    'alamat' => $request->alamat,
    'harga_per_bulan' => $request->harga_per_bulan,
    'jenis_kost' => $request->jenis_kost,
    'jumlah_kamar' => $request->jumlah_kamar,
    'kamar_tersedia' => $request->kamar_tersedia,
    'tersedia' => $request->kamar_tersedia > 0,
    'fasilitas' => json_decode($request->fasilitas, true),
    'thumbnail' => $thumbnail,
    'gambar_kamar_mandi' => $gambarKamarMandi,
    'gambar_interior' => $gambarInterior,
    'gambar_depan' => $gambarDepan,
]);

        return response()->json([
            'success' => true,
            'message' => 'Kos berhasil ditambahkan.',
            'data' => $kost
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $kost = Kost::find($id);

        if (!$kost) {
            return response()->json([
                'success' => false,
                'message' => 'Data kos tidak ditemukan.'
            ], 404);
        }

        if (auth()->user()->role !== 'super_admin' && $kost->owner_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda hanya dapat mengubah data kos milik Anda sendiri.'
            ], 403);
        }

        $request->validate([
            'nama_kost' => 'sometimes|string|max:255',
            'alamat' => 'sometimes|string',
            'harga_per_bulan' => 'sometimes|integer|min:0',
            'jenis_kost' => 'sometimes|in:putra,putri,campur',
            'jumlah_kamar' => 'sometimes|integer|min:1',
            'kamar_tersedia' => 'sometimes|integer|min:0',
            'fasilitas' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'gambar_kamar_mandi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'gambar_interior' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'gambar_depan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only([
            'nama_kost',
            'alamat',
            'harga_per_bulan',
            'jenis_kost',
            'jumlah_kamar',
            'kamar_tersedia',
            'fasilitas'
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('kosts', 'public');
        }

        if ($request->hasFile('gambar_kamar_mandi')) {
            $data['gambar_kamar_mandi'] = $request->file('gambar_kamar_mandi')->store('kosts', 'public');
        }

        if ($request->hasFile('gambar_interior')) {
            $data['gambar_interior'] = $request->file('gambar_interior')->store('kosts', 'public');
        }

        if ($request->hasFile('gambar_depan')) {
            $data['gambar_depan'] = $request->file('gambar_depan')->store('kosts', 'public');
        }
if ($request->has('fasilitas')) {
    $data['fasilitas'] = json_decode($request->fasilitas, true);
}  
        $kost->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Kos berhasil diupdate.',
            'data' => $kost
        ], 200);
    }

    public function destroy($id)
    {
        $kost = Kost::find($id);

        if (!$kost) {
            return response()->json([
                'success' => false,
                'message' => 'Data kos tidak ditemukan.'
            ], 404);
        }

        if (auth()->user()->role !== 'super_admin' && $kost->owner_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda hanya dapat menghapus data kos milik Anda sendiri.'
            ], 403);
        }

        $kost->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kos berhasil dihapus.'
        ], 200);
    }

    public function myKosts()
    {
        $kosts = Kost::where('owner_id', auth()->id())->get();
try{
        return response()->json([
            'success' => true,
            'message' => 'Data kos milik Anda berhasil diambil.',
            'data' => $kosts
        ], 200);}
        catch (\Exception $e) {
        return response()->json([
            'message' => 'Error',
            'error' => $e->getMessage()
        ], 500);
    }
    }
}