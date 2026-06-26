<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Http\Requests\StoreObatRequest;
use App\Http\Resources\ObatResource;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ObatApiController extends Controller
{
    // 1. GET ALL + Filter Tanggal
    public function index(Request $request)
    {
        $query = Obat::query();

        // Filter berdasarkan TANGGAL KADALUARSA (karena tabel legacy tidak ada created_at)
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tgl_kadaluarsa', [$request->start_date, $request->end_date]);
        }

        $obat = $query->paginate(10);
        return ObatResource::collection($obat);
    }

    // 2. POST (Create)
    public function store(StoreObatRequest $request)
    {
        $validated = $request->validated();
        $validated['stok'] = Obat::hitungStokMasuk(0, $validated['stok']);
        
        $obat = Obat::create($validated);

        return (new ObatResource($obat))
            ->additional(['message' => 'Data obat berhasil dicatat melalui API'])
            ->response()
            ->setStatusCode(201);
    }

    // 3. GET BY ID
    public function show($id)
    {
        try {
            $obat = Obat::findOrFail($id);
            return new ObatResource($obat);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Not Found', 'message' => 'Data tidak ditemukan.'], 404);
        }
    }

    // 4. PUT/PATCH (Update) - TUGAS MANDIRI
    public function update(StoreObatRequest $request, $id)
    {
        try {
            $obat = Obat::findOrFail($id);
            $obat->update($request->validated());

            return (new ObatResource($obat))
                ->additional(['message' => 'Data obat berhasil diperbarui'])
                ->response()
                ->setStatusCode(200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Resource tidak ditemukan',
                'message' => 'Data obat dengan ID ' . $id . ' tidak ada di sistem.'
            ], 404);
        }
    }

    // 5. DELETE (Destroy) - TUGAS MANDIRI
    public function destroy($id)
    {
        try {
            $obat = Obat::findOrFail($id);
            $obat->delete();

            return response()->json([
                'message' => 'Data obat dengan ID ' . $id . ' berhasil dihapus.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Resource tidak ditemukan',
                'message' => 'Data obat dengan ID ' . $id . ' tidak ada di sistem untuk dihapus.'
            ], 404);
        }
    }
}