<?php

namespace App\Http\Middleware;

use Closure;
use Facade\FlareClient\Http\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->permission()->first()->title != 'admin') {
            return response()->json([
                'error' => 'Forbidden'
            ],403);
        }
        return $next($request);
    }
}
