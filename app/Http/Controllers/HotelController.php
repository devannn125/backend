<?php

namespace App\Http\Controllers;

use App\Models\Hotels;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Hotels::with('facilities');

        if ($request->filled('kota')) {
            $query->byKota($request->kota);
        }

        if ($request->filled('rating')) {
            $query->minRating((float) $request->rating);
        }

        return response()->json(['success' => true, 'data' => $query->paginate($request->get('per_page', 10))]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_hotel' => 'required|string|max:100',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
            'email' => 'nullable|email|max:100',
            'no_hp' => 'nullable|string|max:20',
            'hotel_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('hotel_image')) {
            $validated['hotel_image'] = $request->file('hotel_image')->store('hotels', 'public');
        }

        return response()->json(['success' => true, 'data' => Hotels::create($validated)->load('facilities')], 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Hotels::with(['facilities', 'rooms'])->findOrFail($id)]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $hotel = Hotels::findOrFail($id);
        $validated = $request->validate([
            'nama_hotel' => 'sometimes|string|max:100',
            'alamat' => 'sometimes|string',
            'kota' => 'sometimes|string|max:50',
            'deskripsi' => 'sometimes|nullable|string',
            'rating' => 'sometimes|nullable|numeric|min:0|max:5',
            'email' => 'sometimes|nullable|email|max:100',
            'no_hp' => 'sometimes|nullable|string|max:20',
            'hotel_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('hotel_image')) {
            if ($hotel->hotel_image) {
                Storage::disk('public')->delete($hotel->hotel_image);
            }
            $validated['hotel_image'] = $request->file('hotel_image')->store('hotels', 'public');
        }

        $hotel->update($validated);
        return response()->json(['success' => true, 'data' => $hotel->fresh('facilities')]);
    }

    public function destroy(string $id): JsonResponse
    {
        Hotels::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function kotaList(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Hotels::select('kota')->distinct()->orderBy('kota')->pluck('kota')]);
    }
}
