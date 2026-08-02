<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->has_admin) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden.',
                ], 403);
            }

            return redirect()
                ->route('admin.login')
                ->with('error', 'Admin access required.');
        }

        return $next($request);
    }
}
