<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Logged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $method = $request->method();

        if (auth()->check()) {
            return $next($request);
        }

        if ($method == 'GET') {
            return redirect()->route('login');
        }

        return response()->json([
            'success' => false,
            'message' => 'Please login to continue',
        ]);
    }
}
