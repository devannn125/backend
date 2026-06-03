<?php

namespace App\Http\Controllers;

use App\Models\Hotels;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HotelFacilitiesController extends Controller
{
    public function index(string $id_hotel): JsonResponse
    {
        $hotel = Hotels::findOrFail($id_hotel);
        return response()->json(['success' => true, 'data' => $hotel->facilities()->get()]);
    }

    public function store(Request $request, string $id_hotel): JsonResponse
    {
        $hotel = Hotels::findOrFail($id_hotel);
        $request->validate([
            'id_facility' => 'required',
            'id_facility.*' => 'string|exists:facilities,id_facility',
        ]);

        $ids = (array) $request->id_facility;
        $hotel->facilities()->syncWithoutDetaching($ids);

        return response()->json(['success' => true, 'data' => $hotel->facilities()->get()], 201);
    }

    public function sync(Request $request, string $id_hotel): JsonResponse
    {
        $hotel = Hotels::findOrFail($id_hotel);
        $request->validate([
            'id_facility' => 'required|array',
            'id_facility.*' => 'string|exists:facilities,id_facility',
        ]);
        $hotel->facilities()->sync($request->id_facility);

        return response()->json(['success' => true, 'data' => $hotel->facilities()->get()]);
    }

    public function destroy(string $id_hotel, string $id_facility): JsonResponse
    {
        $hotel = Hotels::findOrFail($id_hotel);
        $hotel->facilities()->detach($id_facility);

        return response()->json(['success' => true]);
    }

    public function destroyAll(string $id_hotel): JsonResponse
    {
        $hotel = Hotels::findOrFail($id_hotel);
        $hotel->facilities()->detach();

        return response()->json(['success' => true]);
    }
}
