<?php

namespace App\Http\Middleware;

use Closure;
use Caeru;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null,$sinsei_or_null = null)
    {
	    if ($sinsei_or_null === 'sinsei') {
		    if ($request->session()->exists('sinsei_user')) {
			    return Caeru::redirect('personal_detail');
		    }
		    return $next($request);
		    
		
	    } else {
		    if (Auth::guard($guard)->check()) {
			    return Caeru::redirect('dashboard');
		    }
		    return $next($request);
	    }
    }
}
