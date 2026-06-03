<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(User::select('id_user', 'nama', 'email', 'no_hp', 'alamat', 'user_image', 'created_at')->latest('created_at')->paginate(10));
    }

    public function show(string $id): JsonResponse
    {
        return response()->json(User::findOrFail($id)->makeHidden('password'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama'       => 'required|string|max:100',
            'email'      => 'required|email|max:100|unique:user,email',
            'password'   => ['required', Password::min(8)->letters()->numbers()],
            'no_hp'      => 'nullable|string|max:20',
            'alamat'     => 'nullable|string',
            'user_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('user_image')) {
            $validated['user_image'] = $request->file('user_image')->store('user', 'public');
        }

        return response()->json([
            'success' => true,
            'data'    => User::create($validated)->makeHidden('password'),
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nama'       => 'sometimes|string|max:100',
            'email'      => 'sometimes|email|max:100|unique:user,email,' . $id . ',id_user',
            'password'   => ['sometimes', Password::min(8)->letters()->numbers()],
            'no_hp'      => 'nullable|string|max:20',
            'alamat'     => 'nullable|string',
            'user_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('user_image')) {
            if ($user->user_image) {
                Storage::disk('public')->delete($user->user_image);
            }
            $validated['user_image'] = $request->file('user_image')->store('user', 'public');
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $user->fresh()->makeHidden('password'),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        User::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama'       => 'required|string|max:100',
            'email'      => 'required|email|max:100|unique:user,email',
            'password'   => ['required', Password::min(8)->letters()->numbers()],
            'no_hp'      => 'nullable|string|max:20',
            'alamat'     => 'nullable|string',
            'user_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('user_image')) {
            $validated['user_image'] = $request->file('user_image')->store('user', 'public');
        }

        $user  = User::create($validated);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'data'    => $user->makeHidden('password'),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'data'    => $user->makeHidden('password'),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out.',
        ]);
    }
}