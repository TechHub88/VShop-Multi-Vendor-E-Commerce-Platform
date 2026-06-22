<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;

class ClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Redirect if not logged in
        if (!Auth::check()) {
            return redirect()->route('frontend.frontendlogin')->with('error', 'Please login first.');
        }

        // Redirect if the user is not client
        if (auth()->user()->role !== 'client') {
            Auth::logout();
            return redirect()->route('frontend.frontendlogin')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}
