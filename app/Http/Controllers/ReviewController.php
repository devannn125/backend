<?php

namespace App\Http\Controllers;

use App\Models\Reviews;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Reviews::with(['user:id_user,nama', 'hotel:id_hotel,nama_hotel']);

        if ($request->filled('id_hotel')) {
            $query->where('id_hotel', $request->id_hotel);
        }

        if ($request->filled('id_user')) {
            $query->where('id_user', $request->id_user);
        }

        if ($request->filled('min_rating')) {
            $query->minRating($request->integer('min_rating'));
        }

        return response()->json($query->orderByDesc('created_at')->paginate(10));
    }

    public function show(string $id): JsonResponse
    {
        return response()->json(Reviews::with(['user:id_user,nama', 'hotel:id_hotel,nama_hotel'])->findOrFail($id));
    }

    public function summary(string $id_hotel): JsonResponse
    {
        return response()->json(Reviews::where('id_hotel', $id_hotel)
            ->selectRaw('COUNT(*) AS total_review, ROUND(AVG(rating), 1) AS rata_rata_rating')
            ->first());
    }

    public function store(Request $request): JsonResponse
    {
        // 1. Terima kembali id_hotel dari Flutter
        $validated = $request->validate([
            'id_user' => 'required|string|exists:user,id_user', // atau 'users' tergantung nama tabel Anda
            'id_booking' => 'required|string|exists:bookings,id_booking',
            'id_hotel' => 'required|string', // 🟢 DITAMBAHKAN LAGI
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string',
        ]);

        $booking = \App\Models\Bookings::where('id_booking', $validated['id_booking'])->firstOrFail();

        // Validasi agar tidak review 2x
        if (\App\Models\Reviews::where('id_booking', $booking->id_booking)->exists()) {
            return response()->json(['message' => 'Anda sudah mereview pesanan ini.'], 400);
        }

        // Simpan Review Induk
        $review = \App\Models\Reviews::create([
            'id_user' => $validated['id_user'],
            'id_booking' => $booking->id_booking,
            'id_hotel' => $validated['id_hotel'], // 🟢 GUNAKAN DATA DARI FLUTTER
            'rating' => $validated['rating'],
            'komentar' => $validated['komentar'] ?? null,
        ]);

        // Simpan Multiple Media jika ada (Silakan di-uncomment jika tabel review_media sudah dibuat)
        /*
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('reviews', 'public');
                \App\Models\ReviewMedia::create([
                    'id_review' => $review->id_review,
                    'media_path' => $path,
                ]);
            }
        }
        */

        return response()->json([
            'success' => true, 
            'message' => 'Review berhasil dikirim',
            'data' => $review
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $review = Reviews::findOrFail($id);
        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'komentar' => 'nullable|string',
        ]);
        $review->update($validated);

        return response()->json(['success' => true, 'data' => $review->fresh()->load(['user:id_user,nama', 'hotel:id_hotel,nama_hotel'])]);
    }

    public function destroy(string $id): JsonResponse
    {
        Reviews::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
