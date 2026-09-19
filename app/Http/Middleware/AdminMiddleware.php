<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class AdminMiddleware
{
    // ==================== Admin Access Check ====================

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (!$user || !$user->hasRole('admin')) {
            abort(403);
        }

        return $next($request);
    }
}
