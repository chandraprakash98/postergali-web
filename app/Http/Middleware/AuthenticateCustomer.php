<?php

namespace App\Http\Middleware;

use App\Models\CustomerAccessToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCustomer
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated: Bearer token is required.',
            ], 401);
        }

        $plainTextToken = trim(substr($header, 7));

        if (empty($plainTextToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated: Empty bearer token provided.',
            ], 401);
        }

        $accessToken = CustomerAccessToken::findValidToken($plainTextToken);

        if (!$accessToken || !$accessToken->customer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated: Invalid or expired token.',
            ], 401);
        }

        // Update token last used at timestamp without updating model timestamps unnecessarily
        $accessToken->timestamps = false;
        $accessToken->update(['last_used_at' => now()]);
        $accessToken->timestamps = true;

        // Set customer on request attributes and user resolver
        $customer = $accessToken->customer;
        $request->attributes->set('customer', $customer);
        $request->setUserResolver(fn () => $customer);

        return $next($request);
    }
}
