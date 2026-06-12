<?php

namespace App\Http\Controllers;

use App\Models\BookingDetails;
use App\Models\Bookings;
use App\Models\Payments;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    // Relasi yang selalu di-load untuk history Flutter
    private const WITH_HISTORY = [
        'bookingDetails.room.hotel',
        'payments',
    ];

    // Relasi tambahan untuk detail lengkap
    private const WITH_DETAIL = [
        'bookingDetails.room.hotel',
        'bookingDetails.room.roomType',
        'payments',
        'bookingAddons.addon',
        'user',
    ];

    public function index(Request $request): JsonResponse
    {
        // Gunakan authenticated user, bukan dari request body
        $userId = $request->user()->id_user;

        $query = Bookings::with(self::WITH_HISTORY)
            ->byUser($userId)
            ->orderByDesc('tanggal_booking');

        if ($request->filled('status')) {
            $query->status($request->status);
        }

        return response()->json([
            'success' => true,
            'data'    => $query->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_room'           => 'required|string|exists:rooms,id_room',
            'check_in'          => 'required|date|after_or_equal:today',
            'check_out'         => 'required|date|after:check_in',
            'harga_per_malam'   => 'required|numeric|min:0',
            'total_harga'       => 'required|numeric|min:0',
            'metode_pembayaran' => ['required', Rule::in([
                'transfer', 'ewallet', 'credit_card', 'virtual_account', 'qris',
            ])],
        ]);

        $booking = DB::transaction(function () use ($request, $validated) {
            $userId      = $request->user()->id_user;
            $checkIn     = Carbon::parse($validated['check_in']);
            $checkOut    = Carbon::parse($validated['check_out']);
            $jumlahMalam = $checkIn->diffInDays($checkOut);

            // 1. Buat booking
            $booking = Bookings::create([
                'id_user'         => $userId,
                'tanggal_booking' => now(),
                'check_in'        => $validated['check_in'],
                'check_out'       => $validated['check_out'],
                'total_harga'     => $validated['total_harga'],
                'status'          => Bookings::STATUS_PENDING,
            ]);

            // 2. Buat booking_detail — penghubung ke room & hotel
            BookingDetails::create([
                'id_booking'   => $booking->id_booking,
                'id_room'      => $validated['id_room'],
                'harga'        => $validated['harga_per_malam'],
                'jumlah_malam' => $jumlahMalam,
                'subtotal'     => $validated['harga_per_malam'] * $jumlahMalam,
                'status'       => BookingDetails::STATUS_PENDING,
            ]);

            // 3. Buat payment
            Payments::create([
                'id_booking'        => $booking->id_booking,
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'jumlah_bayar'      => $validated['total_harga'],
                'status_pembayaran' => Payments::STATUS_PENDING,
                'expired_at'        => now()->addHours(24),
            ]);

            return $booking;
        });

        return response()->json([
            'success' => true,
            'data'    => $booking->load(self::WITH_DETAIL),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $booking = Bookings::with(self::WITH_DETAIL)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $booking,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $booking   = Bookings::findOrFail($id);
        $validated = $request->validate([
            'check_in'    => 'sometimes|required|date',
            'check_out'   => 'sometimes|required|date|after:check_in',
            'total_harga' => 'sometimes|required|numeric|min:0',
            'status'      => ['sometimes', 'required', Rule::in(['success', 'pending', 'cancel'])],
        ]);

        $booking->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $booking->fresh(self::WITH_DETAIL),
        ]);
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['success', 'pending', 'cancel'])],
        ]);

        $booking = Bookings::findOrFail($id);
        $booking->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $booking->fresh(self::WITH_HISTORY),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        Bookings::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    public function rekap(): JsonResponse
    {
        $data = Bookings::selectRaw(
            'status, COUNT(*) as total, SUM(total_harga) as total_pendapatan'
        )
        ->groupBy('status')
        ->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function downloadPdf(string $id)
    {
        $booking = Bookings::with(self::WITH_DETAIL)->findOrFail($id);
        $payment = $booking->payments->first();

        $data = [
            'booking' => $booking,
            'payment' => $payment,
            'detail'  => $booking->bookingDetails->first(),
            'user'    => $booking->user
        ];

        $pdf = Pdf::loadView('pdf.invoice', $data);

        return $pdf->download('invoice-' . $booking->id_booking . '.pdf');
    }
}