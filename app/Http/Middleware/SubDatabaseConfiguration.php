<?php

namespace App\Http\Middleware;

use Closure;
use Config;
use DB;
use App\Exceptions\UndefinedCompanyException;
use App\Exceptions\UnauthenticatedCompanyException;

class SubDatabaseConfiguration
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
        $company_code = $request->route('company_code');

        if ($this->checkCompany($company_code)) {

            $this->connectToSubDatabase($company_code);

            // Only if the user is already authenticated, will the 'company_code' route parameter be removed.
            if ($request->user()) {
                $request->route()->forgetParameter('company_code');
            }

            return $next($request);
        }

    }

    /**
     * Check if there is a company with this company code in the caeru_main database.
     *
     * @param  string  $company_code
     * @return boolean
     */
    protected function checkCompany($company_code)
    {
        $current_company_code = session('current_company_code');

        if ($current_company_code && $current_company_code != $company_code)
            throw new UnauthenticatedCompanyException();
        elseif (!DB::table('companies')->where('company_code', $company_code)->exists())
            throw new UndefinedCompanyException();
        else
            return true;
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
