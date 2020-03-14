<?php

namespace App\Listeners\Reusables;


use App\EmployeeWorkingDay;
use App\EmployeeWorkingInformation;
use App\ChecklistItem;
use App\ChecklistErrorTimer;

trait RelateToChecklistErrorTrait
{
    /**
     * Clear all the timestamp errors relate to a given EmployeeWorkingDay except from a given array of exceptions.
     *
     * @param EmployeeWorkingDay    $working_day
     * @return void
     */
    protected function resetTimestampErrorRecords(EmployeeWorkingDay $working_day, $exceptions = [])
    {
        if ($working_day) {
            ChecklistItem::where('date', '=', $working_day->date)->where('employee_id', '=', $working_day->employee_id)->where('item_type', '=', ChecklistItem::TIMESTAMP_ERROR)->whereNotIn('error_type', $exceptions)->delete();
        }
    }

    /**
     * Reset the records of a specific type of TimestampError (it's either FORGOT_END_WORK_ERROR or FORGOT_RETURN_ERROR)
     * These types of TimestampError need to be reset a two tables.
     *
     * @param EmployeeWorkingDay    $working_day
     * @return void
     */
    protected function resetTimestampErrorRecordsOfASpecificType(EmployeeWorkingDay $working_day, $error_type)
    {
        // You have to reset the records in two table:
        if ($working_day) {

            // 1. table checklist_items of this specific company
            ChecklistItem::where('date', '=', $working_day->date)->where('employee_id', '=', $working_day->employee_id)->where('item_type', '=', ChecklistItem::TIMESTAMP_ERROR)->where('error_type', $error_type)->delete();

            // 2. table checklist_error_timers of the main database of Caeru project
            ChecklistErrorTimer::where('company_code', '=', $working_day->employee->workLocation->company->code)
                                ->where('employee_id', '=', $working_day->employee->id)
                                ->where('date', '=', $working_day->date)
                                ->where('timestamp_error_type', '=', $error_type)->delete();
        }
    }


    /**
     * Clear all the confirm_needed errors relate to a given EmployeeWorkingDay (exclude some provided types)
     *
     * @param EmployeeWorkingInformation    $working_info
     * @param EmployeeWorkingDay            $working_day
     * @param array                         $exception
     * @return void
     */
    protected function resetConfirmNeededErrorRecords(EmployeeWorkingInformation $working_info, EmployeeWorkingDay $working_day, $exceptions = [])
    {
        if ($working_day) {
            ChecklistItem::where('date', '=', $working_day->date)
                            ->where('employee_id', '=', $working_day->employee_id)
                            ->where('item_type', '=', ChecklistItem::CONFIRM_NEEDED)
                            ->where('employee_working_information_id', '=', $working_info->id)
                            ->whereNotIn('error_type', $exceptions)->delete();
        }
    }

    /**
     * Clear all the confirm_needed errors of a given type relate to a given EmployeeWorkingDay
     *
     * @param EmployeeWorkingInformation    $working_info
     * @param EmployeeWorkingDay            $working_day
     * @param integer                       $error_type
     * @return void
     */
    protected function resetConfirmNeededErrorRecordsOfASpecificType(EmployeeWorkingInformation $working_info, EmployeeWorkingDay $working_day, $error_type)
    {
        if ($working_day) {
            ChecklistItem::where('date', '=', $working_day->date)
                            ->where('employee_id', '=', $working_day->employee_id)
                            ->where('item_type', '=', ChecklistItem::CONFIRM_NEEDED)
                            ->where('employee_working_information_id', '=', $working_info->id)
                            ->where('error_type', '=', $error_type)->delete();
        }
    }
}