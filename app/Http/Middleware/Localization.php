<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if ($locale && in_array($locale, ['id', 'en', 'zh'])) {
            App::setLocale($locale);
            URL::defaults(['locale' => $locale]);
            
            $request->route()->forgetParameter('locale');

            $response = $next($request);
            if (method_exists($response, 'cookie')) {
                return $response->cookie('locale', $locale, 30 * 24 * 60);
            }
            return $response;
        }

        return $next($request);
    }
}
