<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class EnsureDeliveryAccess
{
    // ==================== Seller Access Check ====================

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (
            !$user ||
            (!$user->hasRole('delivery') && !$user->hasRole('admin'))
        ) {
            abort(403, 'You do not have permission to access .');
        }

        return $next($request);
    }
}
