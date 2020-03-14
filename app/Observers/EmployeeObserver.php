<?php

namespace App\Observers;

use App\WorkLocation;
use App\Employee;
use Carbon\Carbon;

// This class's purpose is to handle the view_order of the model 
class EmployeeObserver
{

    /**
     * Listen to the created event of employee and assign an appropriate holidays_update_day for that employee
     *
     * @param Eloquent $employee
     * @return void
     */
    public function creating($employee)
    {
        $work_location = WorkLocation::find($employee->work_location_id);

        if ($work_location) {
            $month_number = $work_location->currentSetting()->paid_holiday_after_joined_period;

            $carbon_start_using_paid_holiday_date = new Carbon($employee->joined_date);

            if (isset($month_number)) {
                $carbon_start_using_paid_holiday_date->addMonths($month_number);
            }
            $employee->holidays_update_day = $carbon_start_using_paid_holiday_date->format('m/d');

        } else {
            throw new \Exception('This WorkLocation does not exist!!');
        }
    }

}