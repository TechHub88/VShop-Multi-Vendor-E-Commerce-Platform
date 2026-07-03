<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BlockIpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // Get blocked IPs from settings
        $blockedIpsJson = DB::table('settings')->where('key', 'blocked_ips')->value('value');
        if ($blockedIpsJson) {
            $blockedIps = json_decode($blockedIpsJson, true);
            if (is_array($blockedIps)) {
                $ips = array_column($blockedIps, 'ip');
                if (in_array($ip, $ips)) {
                    abort(Response::HTTP_FORBIDDEN, "Your IP address {$ip} has been blocked by the administrator.");
                }
            }
        }

        return $next($request);
    }
}
