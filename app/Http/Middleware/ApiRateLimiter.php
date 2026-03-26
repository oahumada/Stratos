<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ApiRateLimiter
{
    public function handle(Request $request, Closure $next)
    {
        // Get rate limit based on route type
        $limit = $this->getLimit($request);
        $key = $this->getKey($request);

        // Check if limit exceeded
        if (RateLimiter::tooManyAttempts($key, $limit)) {
            return response()->json([
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key),
            ], 429);
        }

        // Record the attempt
        RateLimiter::hit($key, 60); // 60-second window

        $response = $next($request);

        // Add headers to response (correct way for Symfony Response)
        $response->headers->set('X-RateLimit-Limit', (string) $limit);
        $response->headers->set('X-RateLimit-Remaining', (string) $this->getRemainingAttempts($key, $limit));
        // X-RateLimit-Reset: UNIX timestamp when the limit resets
        $resetIn = RateLimiter::availableIn($key);
        $response->headers->set('X-RateLimit-Reset', (string) (time() + $resetIn));

        return $response;
    }

    /**
     * Determine rate limit based on request type and authentication  
     */
    protected function getLimit(Request $request): int
    {
        // Public routes: strict
        if ($this->isPublicRoute($request)) {
            return 30; // 30 requests per minute
        }

        // Authenticated users
        if ($request->user()) {
            return 300; // 300 requests per minute
        }

        // Fallback for unauthenticated
        return 60; // 60 requests per minute
    }

    /**
     * Generate rate limit key
     */
    protected function getKey(Request $request): string
    {
        $identifier = $request->user()?->id ?? $request->ip();
        $route = $request->route()?->getName() ?? 'anonymous';

        return "rate_limit:{$route}:{$identifier}";
    }

    /**
     * Get remaining attempts
     */
    protected function getRemainingAttempts(string $key, int $limit): int
    {
        $remaining = $limit - RateLimiter::attempts($key);
        return max(0, $remaining);
    }

    /**
     * Check if route is public (doesn't require authentication)
     */
    protected function isPublicRoute(Request $request): bool
    {
        $path = $request->path();

        // Public route patterns
        $publicPatterns = [
            'api/catalogs',
            'api/assessments/feedback',
            'api/approvals/',
            'api/career/',
            'api/compliance/public',
            'api/public/',
        ];

        foreach ($publicPatterns as $pattern) {
            if (str_starts_with($path, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
