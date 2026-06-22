<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $type
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $type)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/admin/login/products');
        }
        
        if ($user->type !== $type) {
            // Redirect based on user type
            if ($user->type === 'travel') {
                return redirect('/vtravel/admin/dashboard');
            } elseif ($user->type === 'product') {
                return redirect('/admin/dashboard');
            } else {
                Auth::logout();
                return redirect('/admin/login/products');
            }
        }
        
        return $next($request);
    }
}