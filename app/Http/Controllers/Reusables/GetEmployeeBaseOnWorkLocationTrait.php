<?php

namespace App\Http\Controllers\Reusables;

use Illuminate\Support\Facades\Auth;
use App\Employee;
use App\WorkLocation;

trait GetEmployeeBaseOnWorkLocationTrait
{
    /**
     * Get the list of employee base on the current work location which the manager is using.
     *
     * @return QueryBuilder
     */
    private function getEmployeesBaseOnCurrentWorkLocation()
    {
        $chosen_work_location = session('current_work_location');

        $employees_list = null;

        if ($chosen_work_location == "all") {

            $employees_list = Auth::user()->company->employees()->workLocationEnable();

        } elseif (is_array($chosen_work_location)) {

            $employees_list = Employee::whereIn('work_location_id', $chosen_work_location)->workLocationEnable();

        } else {

            $employees_list = Employee::where('work_location_id', $chosen_work_location);
        }

        return $employees_list;
    }
}