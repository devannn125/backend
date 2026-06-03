<?php

namespace App\Http\Controllers;

use App\Models\Rooms;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Rooms::with(['hotel', 'roomType']);

        if ($request->filled('id_hotel')) {
            $query->where('id_hotel', $request->id_hotel);
        }

        if ($request->filled('id_room_type')) {
            $query->where('id_room_type', $request->id_room_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate(10));
    }

    public function available(Request $request): JsonResponse
    {
        $query = Rooms::available()->with('roomType');

        if ($request->filled('id_hotel')) {
            $query->where('id_hotel', $request->id_hotel);
        }

        return response()->json($query->get());
    }

    public function show(string $id): JsonResponse
    {
        return response()->json(Rooms::with(['hotel', 'roomType'])->findOrFail($id));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_hotel' => 'required|string|max:100|exists:hotels,id_hotel',
            'id_room_type' => 'required|string|max:100|exists:room_types,id_room_type',
            'room_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'nomor_kamar' => 'required|string|max:10',
            'status' => ['required', Rule::in(['available', 'booked', 'maintenance'])],
        ]);

        if ($request->hasFile('room_image')) {
            $validated['room_image'] = $request->file('room_image')->store('rooms', 'public');
        }

        return response()->json(['success' => true, 'data' => Rooms::create($validated)->load(['hotel', 'roomType'])], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $room = Rooms::findOrFail($id);
        $validated = $request->validate([
            'id_hotel' => 'sometimes|string|max:100|exists:hotels,id_hotel',
            'id_room_type' => 'sometimes|string|max:100|exists:room_types,id_room_type',
            'room_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'nomor_kamar' => 'sometimes|string|max:10',
            'status' => ['sometimes', Rule::in(['available', 'booked', 'maintenance'])],
        ]);

        if ($request->hasFile('room_image')) {
            if ($room->room_image) {
                Storage::disk('public')->delete($room->room_image);
            }
            $validated['room_image'] = $request->file('room_image')->store('rooms', 'public');
        }

        $room->update($validated);
        return response()->json(['success' => true, 'data' => $room->fresh(['hotel', 'roomType'])]);
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate(['status' => ['required', Rule::in(['available', 'booked', 'maintenance'])]]);
        $room = Rooms::findOrFail($id);
        $room->update($validated);

        return response()->json(['success' => true, 'data' => $room->fresh()]);
    }

    public function destroy(string $id): JsonResponse
    {
        Rooms::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
