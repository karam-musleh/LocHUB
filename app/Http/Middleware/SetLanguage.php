<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

    if ($request->has('lang')) {
            $lang = $request->query('lang');
        } elseif ($request->session()->has('lang')) {
            $lang = $request->session()->get('lang');
        } else {
            $lang = config('app.locale');
        }
        $request->session()->put('lang', $lang);
        App::setlocale($lang);
        return $next($request);
    }
}
