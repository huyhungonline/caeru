<?php

namespace App;

class Setting extends Model
{
    /**
     * Constants for the usage of the go_out_button
     */
    const USE_GO_OUT_BUTTON            = 0;
    const USE_AS_BREAK_TIME_BUTTON     = 1;
    const NOT_USE_GO_OUT_BUTTON        = 2;

    /**
     * Constants for the pay_month
     */
    const THIS_MONTH            = 1;
    const NEXT_MONTH            = 2;
    const NEXT_NEXT_MONTH       = 3;

    /**
     * Scope work location
     *
     * @param QueryBuilder $query
     * @param mixed $work_location_id
     *
     * @return QueryBuilder
     */
    public function scopeWorkLocations($query, $work_location_id=null)
    {
        return $query->whereWorkLocationId($work_location_id);
    }

    /**
     * Gets the salary day in setting.
     *
     * @param $work_location_id
     *
     * @return QueryBuilder.
     */
    public function getSalaryDay($work_location_id)
    {
        return $this->workLocations($work_location_id)->get(['salary_accounting_day'])->first()->salary_accounting_day??$this->workLocations()->get(['salary_accounting_day'])->first()->salary_accounting_day??false;
    }

}
