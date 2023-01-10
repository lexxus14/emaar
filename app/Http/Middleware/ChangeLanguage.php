<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
//use Illuminate\Contracts\Foundation\Application;
use Closure;

class ChangeLanguage
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
        app()->setLocale(Session::get('locale'));
        // Lang::setLocale(Session::get('locale'));
        return $next($request);
    }
}
