<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Issue a JWT for valid credentials. Inactive users cannot authenticate.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        if (! $user->is_active) {
            return response()->json([
                'message' => 'This account has been deactivated.',
            ], 403);
        }

        $token = JWTAuth::fromUser($user);
        $ttlMinutes = (int) config('jwt.ttl');

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $ttlMinutes * 60,
        ]);
    }

    /**
     * Confirm the caller may access guest-only routes (e.g. login).
     */
    public function guestSession(): JsonResponse
    {
        return response()->json([
            'guest' => true,
        ]);
    }

    /**
     * Return the authenticated user for session checks.
     */
    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'authenticated' => true,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }
}
