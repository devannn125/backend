<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Bookings::with(['user', 'bookingAddons.addon']);

        if ($request->filled('id_user')) {
            $query->byUser($request->id_user);
        }

        if ($request->filled('status')) {
            $query->status($request->status);
        }

        return response()->json(['success' => true, 'data' => $query->orderByDesc('tanggal_booking')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_user' => 'required|string|max:100|exists:user,id_user',
            'tanggal_booking' => 'required|date',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'total_harga' => 'required|numeric|min:0',
            'status' => ['required', Rule::in(['success', 'pending', 'cancel'])],
        ]);

        return response()->json(['success' => true, 'data' => Bookings::create($validated)->load(['user', 'bookingAddons.addon'])], 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Bookings::with(['user', 'bookingAddons.addon'])->findOrFail($id)]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $booking = Bookings::findOrFail($id);
        $validated = $request->validate([
            'id_user' => 'sometimes|required|string|max:100|exists:user,id_user',
            'tanggal_booking' => 'sometimes|required|date',
            'check_in' => 'sometimes|required|date',
            'check_out' => 'sometimes|required|date|after:check_in',
            'total_harga' => 'sometimes|required|numeric|min:0',
            'status' => ['sometimes', 'required', Rule::in(['success', 'pending', 'cancel'])],
        ]);
        $booking->update($validated);

        return response()->json(['success' => true, 'data' => $booking->fresh(['user', 'bookingAddons.addon'])]);
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate(['status' => ['required', Rule::in(['success', 'pending', 'cancel'])]]);
        $booking = Bookings::findOrFail($id);
        $booking->update($validated);

        return response()->json(['success' => true, 'data' => $booking->fresh()]);
    }

    public function destroy(string $id): JsonResponse
    {
        Bookings::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function rekap(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Bookings::selectRaw('status, COUNT(*) as total, SUM(total_harga) as total_pendapatan')->groupBy('status')->get()]);
    }
}
