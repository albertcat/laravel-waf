<?php

namespace Albert\Waf\Http\Middleware;

use Albert\Waf\Facades\Waf;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WafMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if IP is banned
        abort_if(Waf::ipHasBan($request->ip()), 400);

        // TODO: Run waf checks

        // All is good
        return $next($request);
    }
}
