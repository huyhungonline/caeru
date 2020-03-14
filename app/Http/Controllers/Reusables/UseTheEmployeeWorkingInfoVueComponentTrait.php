<?php

namespace App\Http\Controllers\Reusables;

use App\WorkLocation;
use App\Setting;
use Carbon\Carbon;
use NationalHolidays;
use App\EmployeeWorkingDay;
use App\WorkStatus;

trait UseTheEmployeeWorkingInfoVueComponentTrait
{
    /**
     * Extrac data from the setting of given work locations (from given working infos). These data are for the alert function( characters turn to red color when alert).
     *
     * @param array|collection   $working_infos
     * @return array
     */
    protected function extractDataFromWorkLocationSetting($working_infos)
    {
        $data = [];

        foreach ($working_infos as $working_info) {

            $work_location = WorkLocation::find($working_info->planned_work_location_id);

            if ($work_location) {
                $setting = $work_location->currentSetting();

                $data[] = [
                    'working_info_id' => $working_info->id,
                    'start_time_diff_limit' => $setting->start_time_diff_limit,
                    'end_time_diff_limit' => $setting->end_time_diff_limit,
                    'alert_go_out_time' => $setting->go_out_button_usage === Setting::USE_AS_BREAK_TIME_BUTTON,
                ];
            }
        }

        return $data;
    }

    /**
     * Get the schedule-transfering-function-related stuffs to send to Vuejs
     *
     * @param int           $year
     * @param int           $month
     * @param int           $work_location_id
     * @param int           $employee_id
     * @return mix
     */
    protected function getScheduleTransferData($working_infos, $employee_id)
    {
        $data = [];

        foreach ($working_infos as $working_info) {

            $work_location = WorkLocation::find($working_info->planned_work_location_id);

            if ($work_location) {
                $setting = $work_location->currentSetting();

                $today = Carbon::now();
                $start_date = $today->format('Y-m') . '-01';
                $end_date = $today->addMonths(3)->format('Y-m-t');

                $national_holidays = NationalHolidays::get($start_date, $end_date);

                $rest_days = $work_location->getRestDays($start_date, $end_date);

                $transferable_days = $this->getTransferableDays($end_date, $employee_id);

                $data[] = [
                    'working_info_id'       => $working_info->id,
                    'flip_color_day'        => $setting->salary_accounting_day,
                    'national_holidays'     => $national_holidays,
                    'rest_days'             => $rest_days,
                    'transferable_days'     => $transferable_days,
                    'start_date'            => $start_date,
                    'end_date'              => $end_date,
                ];
            }
        }

        return $data;

    }

    /**
     * Get all the working_days of a given employee, to which he/she can transfer schedules(from an already-has-schedule-working-day).
     *
     * @param string        $start_date
     * @param string        $end_date
     * @param int           $employee_id
     * @return array
     */
    protected function getTransferableDays($end_date, $employee_id)
    {
        $in_range_working_days = EmployeeWorkingDay::with('employeeWorkingInformations')->where('employee_id', $employee_id)
                                    ->where('date', '>=', Carbon::now()->toDateString())
                                    ->where('date', '<=', $end_date)
                                    ->notConcluded()->get();

        // $transferable_days = $this->getAllDaysInRangeExcludeFrom($start_date, $end_date, $in_range_working_days->pluck('date')->toArray());
        $transferable_days = [];

        foreach ($in_range_working_days as $working_day) {
            $working_info_with_schedule_working_hour = $working_day->employeeWorkingInformations->first(function ($working_info) {
                return ($working_info->schedule_working_hour !== null) ||
                        ($working_info->planned_work_status_id === WorkStatus::HOUDE) ||
                        ($working_info->planned_work_status_id === WorkStatus::KYUUDE);
            });
            if ($working_info_with_schedule_working_hour === null) {
                $transferable_days[] = $working_day->date;
            }
        }

        return $transferable_days;
    }

    /**
     * Just in case, I created this function. But for now, we dont need it.
     */
    // protected function getAllDaysInRangeExcludeFrom($start_date, $end_date, $exclude_days)
    // {
    //     $dates = [];

    //     $carbon_start = new Carbon($start_date);
    //     $carbon_end = new Carbon($end_date);
    //     for(; $carbon_start->lte($carbon_end); $carbon_start->addDay()) {
    //         $date = $carbon_start->format('Y-m-d');
    //         if (!in_array($date, $exclude_days)) {
    //             $dates[] = $date;
    //         }
    //     }

    //     return $dates;
    // }
}