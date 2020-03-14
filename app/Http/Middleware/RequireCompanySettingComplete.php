<?php

namespace App\Http\Middleware;

use Closure;

class RequireCompanySettingComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $model = null)
    {
        if ($model && (session('current_work_location') !== 'all')) {

            $company = $request->user()->company;

            if ($model === 'calendar') {
                if ($company->initial_calendar_completed)
                    return $next($request);
            } elseif ($model === 'setting') {
                if ($company->initial_setting_completed)
                    return $next($request);
            }

            $request->session()->flash('warning', '会社の登録がされていません。会社権限で登録を行ってください');

            return redirect()->back();
        }

        return $next($request);
    }
}
