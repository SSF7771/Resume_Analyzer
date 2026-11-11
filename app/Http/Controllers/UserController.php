<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        // Sanctum handles token/session issuance
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password')))
            return response()->json(['message' => 'Invalid credentials'], 401);

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function me(Request $request)
    {
        // Debug
        $user = $request->user();
        $token = $request->bearerToken();

        return response()->json([
            'user' => $user, // If this is null, the auth guard failed
        ]);
    }

    public function updateProfile(ProfileRequest $request)
    {
        $request->user()->update($request->validated());
        return response()->json(['user' => $request->user()]);
    }

    public function updatePassword(PasswordRequest $request)
    {
        if (!Hash::check($request->currentPassword, $request->user()->password))
            return response()->json(['message' => 'Current password incorrect'], 401);

        $request->user()->update(['password' => $request->newPassword]);

        return response()->json(['ok' => true]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['ok' => true]);
    }
}
