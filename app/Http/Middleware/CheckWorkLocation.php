<?php

namespace App\Http\Middleware;

use Closure;
use Caeru;

class CheckWorkLocation
{
    /**
     * Handle an incoming request. If there is no work location being chosen currently, redirect to the choose page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  boolean  $singular
     * @return mixed
     */
    public function handle($request, Closure $next, $singular = null)
    {
        $chosen_work_location = $request->session()->get('current_work_location');
        if (!$chosen_work_location) {

            // If the user has authority over only one work location, then we'll just save it to session right here.
            $authorities = $request->user()->workLocations;
            if ($authorities->count() == 1) {
                session(['current_work_location' => $authorities->first()->id]);
                return $next($request);
            }

            $request->session()->put('url.intended', redirect()->getUrlGenerator()->full());
            return Caeru::redirect('choosing', [ 'singular' => $singular == 'singular' ]);
        } else {
            if ($singular && is_array($chosen_work_location)) {
                $request->session()->put('url.intended', redirect()->getUrlGenerator()->full());
                return Caeru::redirect('choosing', [ 'singular' => $singular == 'singular' ]);
            }
        }
        return $next($request);
    }
}
