<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Return dashboard payload for the authenticated user.
     */
    public function show(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'message' => 'Welcome to your dashboard.',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'stats' => [
                'active_projects' => 3,
                'pending_tasks' => 12,
            ],
        ]);
    }
}
