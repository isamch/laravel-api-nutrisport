<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectSite
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        
        // Extract domain extension (.fr, .it, .be)
        $extension = substr($host, strrpos($host, '.'));
        
        // Cache site map for 24 hours
        $siteMap = \Cache::remember('site_map', 86400, function () {
            return \DB::table('sites')
                ->pluck('id', 'country_code')
                ->mapWithKeys(fn($id, $code) => ['.' . strtolower(substr($code, -2)) => $id])
                ->toArray();
        });
        
        // Set site_id if not already provided and domain matches
        if (!$request->has('site_id') && isset($siteMap[$extension])) {
            $request->merge(['site_id' => $siteMap[$extension]]);
        }
        
        return $next($request);
    }
}
