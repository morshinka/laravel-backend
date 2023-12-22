<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['email incorrect'],
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['password incorrect'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'jwt-token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function register(Request $request)
    {
        // membuat validasi
        $request->validate([
            'email' => 'required|email|string',
            'name' => 'required|min:3|max:255',
            'password' => 'required',
        ]);

        // membuat data user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        // membuat token untuk user baru
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'jwt-token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function logout(Request $request)
    {
        // ambil request dari user lalu cari token setelah dapat hapus token
        $request->user()->tokens()->delete();
        // kembalikan nilai dengan mengirimkan pesan
        return response()->json([
            'message' => 'Logout succsesfuly',
        ]);
    }
}
