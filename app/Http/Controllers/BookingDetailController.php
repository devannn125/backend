<?php

namespace App\Http\Controllers;

use App\Models\BookingDetails;
use App\Models\Bookings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingDetailController extends Controller
{
    public function index(string $id_booking): JsonResponse
    {
        Bookings::findOrFail($id_booking);
        return response()->json(['success' => true, 'data' => BookingDetails::with('room')->where('id_booking', $id_booking)->get()]);
    }

    public function store(Request $request, string $id_booking): JsonResponse
    {
        Bookings::findOrFail($id_booking);
        $validated = $request->validate([
            'id_room' => 'required|string|exists:rooms,id_room',
            'harga' => 'required|numeric|min:0',
            'jumlah_malam' => 'required|integer|min:1',
            'status' => ['sometimes', Rule::in(['success', 'pending', 'cancel'])],
        ]);
        $validated['id_booking'] = $id_booking;
        $validated['status'] = $validated['status'] ?? BookingDetails::STATUS_PENDING;

        return response()->json(['success' => true, 'data' => BookingDetails::create($validated)->load('room')], 201);
    }

    public function show(string $id_booking, string $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => BookingDetails::with(['booking', 'room'])->where('id_booking', $id_booking)->findOrFail($id)]);
    }

    public function update(Request $request, string $id_booking, string $id): JsonResponse
    {
        $detail = BookingDetails::where('id_booking', $id_booking)->findOrFail($id);
        $validated = $request->validate([
            'id_room' => 'sometimes|string|exists:rooms,id_room',
            'harga' => 'sometimes|numeric|min:0',
            'jumlah_malam' => 'sometimes|integer|min:1',
            'status' => ['sometimes', Rule::in(['success', 'pending', 'cancel'])],
        ]);
        $detail->update($validated);

        return response()->json(['success' => true, 'data' => $detail->fresh('room')]);
    }

    public function destroy(string $id_booking, string $id): JsonResponse
    {
        BookingDetails::where('id_booking', $id_booking)->findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function confirm(string $id_booking, string $id): JsonResponse
    {
        $detail = BookingDetails::where('id_booking', $id_booking)->findOrFail($id);
        $detail->update(['status' => BookingDetails::STATUS_SUCCESS]);
        return response()->json(['success' => true, 'data' => $detail->fresh('room')]);
    }

    public function cancel(string $id_booking, string $id): JsonResponse
    {
        $detail = BookingDetails::where('id_booking', $id_booking)->findOrFail($id);
        $detail->update(['status' => BookingDetails::STATUS_CANCEL]);
        return response()->json(['success' => true, 'data' => $detail->fresh('room')]);
    }
}
