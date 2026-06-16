<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PaymentsController extends Controller
{
    // Definisikan sekali agar konsisten di semua method
    const PAYMENT_RELATIONS = ['booking.bookingDetails.room.hotel'];

    public function index(Request $request): JsonResponse
    {
        $query = Payments::with(self::PAYMENT_RELATIONS); // ✅

        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }
        if ($request->filled('metode')) {
            $query->where('metode_pembayaran', $request->metode);
        }
        if ($request->filled('id_booking')) {
            $query->where('id_booking', $request->id_booking);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $payments,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_booking'        => 'required|string|exists:bookings,id_booking',
            'metode_pembayaran' => ['required', Rule::in([
                Payments::METODE_EWALLET,
                Payments::METODE_CREDIT_CARD,
                Payments::METODE_VIRTUAL_ACCOUNT,
                'qris',
            ])],
            'jumlah_bayar'      => 'required|numeric|min:0',
            'expired_at'        => 'nullable|date|after:now',
        ]);

        $payment = Payments::create([
            'id_booking'        => $validated['id_booking'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'jumlah_bayar'      => $validated['jumlah_bayar'],
            'status_pembayaran' => Payments::STATUS_PENDING,
            'expired_at'        => $validated['expired_at'] ?? now()->addHours(24),
            'paid_at'           => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dibuat.',
            'data'    => $payment->load(self::PAYMENT_RELATIONS), // ✅
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $payment = Payments::with(self::PAYMENT_RELATIONS)->findOrFail($id); // ✅

        return response()->json([
            'success' => true,
            'data'    => $payment,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $payment = Payments::findOrFail($id);

        $validated = $request->validate([
            'metode_pembayaran' => ['sometimes', Rule::in([
                Payments::METODE_EWALLET,
                Payments::METODE_CREDIT_CARD,
                Payments::METODE_VIRTUAL_ACCOUNT,
            ])],
            'jumlah_bayar'      => 'sometimes|numeric|min:0',
            'status_pembayaran' => ['sometimes', Rule::in([
                Payments::STATUS_SUCCESS,
                Payments::STATUS_PENDING,
                Payments::STATUS_CANCEL,
            ])],
            'expired_at'        => 'sometimes|nullable|date',
            'paid_at'           => 'sometimes|nullable|date',
        ]);

        if (
            isset($validated['status_pembayaran']) &&
            $validated['status_pembayaran'] === Payments::STATUS_SUCCESS &&
            is_null($payment->paid_at)
        ) {
            $validated['paid_at'] = now();
        }

        $payment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil diperbarui.',
            'data'    => $payment->fresh(self::PAYMENT_RELATIONS), // ✅
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $payment = Payments::findOrFail($id);
        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dihapus.',
        ]);
    }

    public function confirm(string $id): JsonResponse
    {
        $payment = Payments::findOrFail($id);

        if ($payment->isSuccess()) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran sudah dikonfirmasi sebelumnya.',
            ], 422);
        }

        if ($payment->isCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran yang dibatalkan tidak dapat dikonfirmasi.',
            ], 422);
        }

        $payment->update([
            'status_pembayaran' => Payments::STATUS_SUCCESS,
            'paid_at'           => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dikonfirmasi.',
            'data'    => $payment->fresh(self::PAYMENT_RELATIONS), // ✅
        ]);
    }

    public function cancel(string $id): JsonResponse
    {
        $payment = Payments::findOrFail($id);

        if ($payment->isSuccess()) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran yang sudah berhasil tidak dapat dibatalkan.',
            ], 422);
        }

        $payment->update([
            'status_pembayaran' => Payments::STATUS_CANCEL,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dibatalkan.',
            'data'    => $payment->fresh(self::PAYMENT_RELATIONS), // ✅
        ]);
    }
}