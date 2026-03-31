<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizeCsvHeader
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        // Only adjust for CSV export endpoints
        $path = $request->path();
        if (str_ends_with($path, 'alerts/export/history') || str_contains($path, 'export/history')) {
            $response->headers->set('Content-Type', 'text/csv');
        }

        return $response;
    }
}
