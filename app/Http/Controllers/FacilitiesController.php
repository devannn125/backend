<?php

namespace App\Http\Controllers;

use App\Models\Facilities;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FacilitiesController extends Controller
{
    /**
     * GET /facilities
     * Tampilkan semua fasilitas
     */
    public function index(Request $request): JsonResponse
    {
        $query = Facilities::query();

        // Pencarian nama fasilitas
        if ($request->filled('search')) {
            $query->where('nama_facility', 'like', '%' . $request->search . '%');
        }

        $facilities = $query->orderBy('nama_facility')->get();

        return response()->json([
            'success' => true,
            'data'    => $facilities,
        ]);
    }

    /**
     * POST /facilities
     * Tambah fasilitas baru
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_facility' => 'required|string|max:100|unique:facilities,nama_facility',
        ]);

        $facility = Facilities::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fasilitas berhasil ditambahkan.',
            'data'    => $facility,
        ], 201);
    }

    /**
     * GET /facilities/{id}
     * Detail fasilitas beserta hotel yang memilikinya
     */
    public function show(string $id): JsonResponse
    {
        $facility = Facilities::with('hotels')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $facility,
        ]);
    }

    /**
     * PUT /facilities/{id}
     * Update nama fasilitas
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $facility = Facilities::findOrFail($id);

        $validated = $request->validate([
            'nama_facility' => 'required|string|max:100|unique:facilities,nama_facility,' . $id . ',id_facility',
        ]);

        $facility->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fasilitas berhasil diperbarui.',
            'data'    => $facility,
        ]);
    }

    /**
     * DELETE /facilities/{id}
     * Hapus fasilitas (otomatis hapus relasi di pivot)
     */
    public function destroy(string $id): JsonResponse
    {
        $facility = Facilities::findOrFail($id);

        // Lepas semua relasi hotel di pivot sebelum hapus
        $facility->hotels()->detach();
        $facility->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fasilitas berhasil dihapus.',
        ]);
    }
}
