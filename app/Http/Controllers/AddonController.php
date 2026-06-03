<?php

namespace App\Http\Controllers;

use App\Models\Addons;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AddonController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Addons::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        return response()->json(['success' => true, 'data' => $query->orderBy('nama_addon')->get()]);
    }

    public function available(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Addons::available()->orderBy('nama_addon')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_addon' => 'required|string|max:100|unique:addons,nama_addon',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'status' => ['required', Rule::in(['available', 'unavailable'])],
        ]);

        return response()->json(['success' => true, 'data' => Addons::create($validated)], 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Addons::findOrFail($id)]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $addon = Addons::findOrFail($id);
        $validated = $request->validate([
            'nama_addon' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('addons', 'nama_addon')->ignore($addon->id_addon, 'id_addon')],
            'deskripsi' => 'sometimes|required|string',
            'harga' => 'sometimes|required|numeric|min:0',
            'status' => ['sometimes', 'required', Rule::in(['available', 'unavailable'])],
        ]);
        $addon->update($validated);

        return response()->json(['success' => true, 'data' => $addon->fresh()]);
    }

    public function destroy(string $id): JsonResponse
    {
        Addons::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function toggleStatus(string $id): JsonResponse
    {
        $addon = Addons::findOrFail($id);
        $addon->update(['status' => $addon->status === Addons::STATUS_AVAILABLE ? Addons::STATUS_UNAVAILABLE : Addons::STATUS_AVAILABLE]);

        return response()->json(['success' => true, 'data' => $addon->fresh()]);
    }
}
