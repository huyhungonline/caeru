<?php

namespace App\Http\Middleware;

use Closure;

class RequireWorkLocation
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
        $work_locations = $request->user()->company->workLocations;

        if ($work_locations->isEmpty()) {

            $request->session()->flash('warning', '勤務地の登録がありません。勤務地を登録後、再度登録を行なってください。');

            return redirect()->back();
        }
        
        return $next($request);
    }
}
