<?php

namespace App\Http\Controllers;

use App\Models\BookingAddons;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingAddonController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = BookingAddons::with(['booking', 'addon']);

        if ($request->filled('id_booking')) {
            $query->where('id_booking', $request->id_booking);
        }

        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_booking' => 'required|string|max:100|exists:bookings,id_booking',
            'id_addon' => 'required|string|max:100|exists:addons,id_addon',
            'quantity' => 'required|integer|min:1',
            'subtotal' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        return response()->json(['success' => true, 'data' => BookingAddons::create($validated)->load(['booking', 'addon'])], 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => BookingAddons::with(['booking', 'addon'])->findOrFail($id)]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $bookingAddon = BookingAddons::findOrFail($id);
        $validated = $request->validate([
            'id_booking' => 'sometimes|required|string|max:100|exists:bookings,id_booking',
            'id_addon' => 'sometimes|required|string|max:100|exists:addons,id_addon',
            'quantity' => 'sometimes|required|integer|min:1',
            'subtotal' => 'sometimes|required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);
        $bookingAddon->update($validated);

        return response()->json(['success' => true, 'data' => $bookingAddon->fresh(['booking', 'addon'])]);
    }

    public function destroy(string $id): JsonResponse
    {
        BookingAddons::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function destroyByBooking(string $idBooking): JsonResponse
    {
        $deleted = BookingAddons::where('id_booking', $idBooking)->delete();
        return response()->json(['success' => true, 'deleted' => $deleted]);
    }
}
