<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\SinseiAuthenticateException;

class SinseiAuthenticated
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
	    if ($request->session()->exists('sinsei_user')){
		    return $next($request);
	    }
	    throw new SinseiAuthenticateException();
    }
}
