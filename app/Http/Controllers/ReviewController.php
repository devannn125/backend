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
        $validated = $request->validate([
            'id_user' => 'required|string|max:100|exists:user,id_user',
            'id_hotel' => 'required|string|max:100|exists:hotels,id_hotel',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string',
        ]);

        return response()->json(['success' => true, 'data' => Reviews::create($validated)->load(['user:id_user,nama', 'hotel:id_hotel,nama_hotel'])], 201);
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
