<?php

namespace App\Http\Controllers;

use App\Models\RoomTypes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomTypesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = RoomTypes::query();

        if ($request->filled('kapasitas')) {
            $query->where('kapasitas', '>=', $request->integer('kapasitas'));
        }

        return response()->json($query->orderBy('harga_per_malam')->paginate(10));
    }

    public function show(string $id): JsonResponse
    {
        return response()->json(RoomTypes::findOrFail($id));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_type' => 'required|string|max:50|unique:room_types,nama_type',
            'kapasitas' => 'required|integer|min:1',
            'harga_per_malam' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        return response()->json(['success' => true, 'data' => RoomTypes::create($validated)], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $roomType = RoomTypes::findOrFail($id);
        $validated = $request->validate([
            'nama_type' => 'sometimes|string|max:50|unique:room_types,nama_type,' . $id . ',id_room_type',
            'kapasitas' => 'sometimes|integer|min:1',
            'harga_per_malam' => 'sometimes|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);
        $roomType->update($validated);

        return response()->json(['success' => true, 'data' => $roomType->fresh()]);
    }

    public function destroy(string $id): JsonResponse
    {
        RoomTypes::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
