<?php

namespace App\Helpers;

use DB;

class NationalHolidaysHelper
{
    /**
     * Check whether or not a date is a national holiday.
     *
     * @param $date     the date in question
     * @return boolean
     */
    public static function is($date = null)
    {
        return DB::table('national_holidays')->where('date', $date ? $date : date('Y-m-d'))->exists();
    }

    /**
     * Get a list of national holidays from start_date to end_date
     *
     * @param string    $start_date
     * @param string    $end_date
     * @return array
     */
    public static function get($start_date = null, $end_date = null)
    {
        $query = DB::table('national_holidays');

        if ($start_date) {
            $query->where('date', '>=', $start_date);
        }

        if ($end_date) {
            $query->where('date', '<=', $end_date);
        }

        return $query->pluck('date');
    }
}