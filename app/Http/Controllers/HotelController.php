<?php

namespace App\Http\Controllers;

use App\Models\Hotels;
use App\Models\RoomTypes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $minPriceSubquery = RoomTypes::selectRaw('MIN(room_types.harga_per_malam)')
            ->join('rooms', 'rooms.id_room_type', 'room_types.id_room_type')
            ->whereColumn('rooms.id_hotel', 'hotels.id_hotel');

        $query = Hotels::with('facilities')
            ->select('hotels.*')
            ->selectSub($minPriceSubquery, 'min_price');

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($builder) use ($search) {
                $builder->where('nama_hotel', 'like', $search)
                    ->orWhere('kota', 'like', $search)
                    ->orWhere('alamat', 'like', $search);
            });
        }

        if ($request->filled('kota')) {
            $query->byKota($request->kota);
        }

        if ($request->filled('rating')) {
            $query->minRating((float) $request->rating);
        }

        if ($request->filled('sort_by')) {
            $sortDir = strtolower($request->get('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

            if ($request->sort_by === 'price') {
                if ($sortDir === 'asc') {
                    $query->orderByRaw('COALESCE(min_price, 999999999) asc');
                } else {
                    $query->orderByRaw('COALESCE(min_price, -1) desc');
                }
            } elseif ($request->sort_by === 'rating') {
                $query->orderBy('rating', $sortDir);
            }
        } else {
            $query->orderByDesc('created_at');
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
