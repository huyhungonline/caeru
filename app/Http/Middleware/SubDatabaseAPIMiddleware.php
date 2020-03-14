<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Config;
use Illuminate\Support\Facades\Hash;

class SubDatabaseAPIMiddleware
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
        if ($request->route()->named('download_installer')) return $next($request);
        $company_code = $request->input('company_code');
        if (DB::table('companies')->where('company_code', $company_code)->exists()) {
            if ($request->route()->named('login') || $request->route()->named('connection')) return $next($request);
            if (isset($request->token) && !empty($request->token)) {
                $auth_token = DB::table('api_auth_tokens')->where('company_code', $company_code)->where('device_id', $request->tablet_id)->first();
                if (Hash::check($request->token, $auth_token->remember_token)) {
                    $this->connectToSubDatabase($company_code);
                    return $next($request);
                }
            }
            return response()->json(['auth_token' => ['The authentication information is invalid.']], 403);
        }
        return response()->json(['company_code' => ['The company code is invalid.']], 403);
    }

    /**
     * Connect to the sub database with this company_code.
     *
     * @param  string  $company_code
     * @return void
     */
    protected function connectToSubDatabase($company_code)
    {
        Config::set('database.default', 'sub');
        Config::set('database.connections.sub.database', 'caeru_' . $company_code);
        Config::set('database.connections.sub.prefix', 'caeru_' . $company_code . '_');

        DB::reconnect('sub');
    }
}
