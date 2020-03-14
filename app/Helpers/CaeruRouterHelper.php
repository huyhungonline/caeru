<?php

namespace App\Helpers;

use Request;
use App\Exceptions\UndefinedCompanyException;

class CaeruRouterHelper
{
    /**
     * A wrapper for the laravel's default route helper.
     * It basically detects the current company code and add it to the route.
     *
     * @param  string  $route
     * @param  array   $parameters
     * @param  boolean $absolute_flag
     * @return mix
     */
    public static function route($route = null, $parameters = [], $absolute_flag = true)
    {

        $merged_parameters = self::getParameterWithCurrentCompanyCode($parameters);

        return route($route, $merged_parameters, $absolute_flag);

    }

    /**
     * A wrapper to call the redirect() helper of laravel.
     * Redirect to a named route with parameters and all.
     *
     * @param  string  $route
     * @param  array   $parameters
     * @param  boolean $absolute_flag
     * @return mix
     */
    public static function redirect($route = null, $parameters = [])
    {
        $merged_parameters = self::getParameterWithCurrentCompanyCode($parameters);

        return redirect()->route($route, $merged_parameters);
    }

    /**
     * Get current company code, then assign it to the array parameter if it does not have it.
     *
     * @param   array     $parameters
     * @return  array
     */
    public static function getParameterWithCurrentCompanyCode($parameters = [])
    {
        // In the case the parameter 'parameters' is not an array (but a single parameter), then it should be put into one.
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }

        if (!isset($parameters['company_code'])) {

            $current_company_code = session('current_company_code') ? session('current_company_code') : Request::route('company_code');

            if (!$current_company_code)
                throw new UndefinedCompanyException();

            $parameters['company_code'] = $current_company_code;

        }

        return $parameters;
    }
}
