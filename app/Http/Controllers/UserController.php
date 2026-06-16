<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Google_Client;

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
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:user,email',
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'user_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('user_image')) {
            $validated['user_image'] = $request->file('user_image')->store('user', 'public');
        }

        return response()->json([
            'success' => true,
            'data' => User::create($validated)->makeHidden('password'),
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|max:100|unique:user,email,' . $id . ',id_user',
            'password' => ['sometimes', Password::min(8)->letters()->numbers()],
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
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
            'data' => $user->fresh()->makeHidden('password'),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user(); // Tidak perlu 'clone' kecuali ada kebutuhan spesifik

        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'user_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);
        $shouldRemoveImage = $request->boolean('remove_image');
        unset($validated['remove_image']);

        // 1. LOGIKA HAPUS FOTO
        // Jika Flutter mengirim 'remove_image' = 'true', maka hapus foto lama
        if ($shouldRemoveImage) {
            if ($user->user_image) {
                Storage::disk('public')->delete($user->user_image);
                $user->user_image = null; // Set ke null di DB
                $user->save();
            }
        }

        // 2. LOGIKA UPLOAD FOTO (Sama seperti sebelumnya)
        if ($request->hasFile('user_image')) {
            $file = $request->file('user_image');

            if ($user->user_image) {
                Storage::disk('public')->delete($user->user_image);
            }

            $extension = $file->getClientOriginalExtension();
            $fileName = $user->id_user . '_profile.' . $extension;
            $path = $file->storeAs('user', $fileName, 'public');
            $validated['user_image'] = $path;
        }

        // 3. UPDATE DATA
        $user->update($validated);

        return response()->json([
            'success' => true,
            'data' => $user->fresh()->makeHidden('password'),
        ]);
    }

    public function updatePrivacy(Request $request): JsonResponse
    {
        $user = clone $request->user();

        $validated = $request->validate([
            'email' => 'sometimes|required|email|max:100|unique:user,email,' . $user->id_user . ',id_user',
            'password' => ['nullable', Password::min(8)->letters()->numbers()],
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'data' => $user->fresh()->makeHidden('password'),
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
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:user,email',
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'user_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('user_image')) {
            $validated['user_image'] = $request->file('user_image')->store('user', 'public');
        }

        $user = User::create($validated);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'data' => $user->makeHidden('password'),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'data' => $user->makeHidden('password'),
        ]);
    }

    // --- FUNGSI BARU: GOOGLE LOGIN ---
    public function googleLogin(Request $request): JsonResponse
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);

        // Pendekatan verifikasi yang lebih fleksibel
        $payload = $client->verifyIdToken($request->id_token);

        if ($payload) {
            $email = $payload['email'];
            $name = $payload['name'];

            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = new User();
                $user->email = $email;
                $user->nama = $name;
                $user->password = Hash::make(Str::random(24));
                // user_image dibiarkan null sesuai keinginan Anda
                $user->save();
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'data' => $user->makeHidden('password'),
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Token Google tidak valid.'
            ], 401);
        }
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
