<?php

namespace App;

class EmployeeWorkingDay extends Model
{

    /**
     * Get the employee of this working day instance
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get all the working information instances of this working day instance
     */
    public function employeeWorkingInformations()
    {
        return $this->hasMany(EmployeeWorkingInformation::class);
    }

    /**
     * Get all the working timestamp instances of this working day instance
     */
    public function workingTimestamps()
    {
        return $this->hasMany(WorkingTimestamp::class);
    }

    //////// Query Scope ///////

    /**
     * Get the EmployeeWorkingDay which has not been concluded yet
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotConcluded($query)
    {
        return $query->where('concluded_level_one', false)->where('concluded_level_two', false);
    }

    /**
     * Get the EmployeeWorkingDay which doesnt have an EmployeeWorkingInfomation that assocciates with a given PlannedSchedule and
     * also has manually_modified flag ON.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $schedule_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotHaveManuallyModifiedEmployeeWorkingInformationThatAssociateWithThisSchedule($query, $schedule_id)
    {
        return $query->whereDoesntHave('employeeWorkingInformations', function($query) use ($schedule_id) {
            $query->where('planned_schedule_id', $schedule_id)->where('manually_modified', true);
        });
    }

    /**
     * Get the EmployeeWorkingDay which doesnt have an EmployeeWorkingInformation that was manually modified and also associates with a given PlannedSchedule
     * but not in a normal way, through WorkAddressWorkingEmployee that is.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param int                                  $schedule_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotHaveManuallyModifiedEmployeeWorkingInformationThatAssociateWithThisScheduleThroughWorkAddressWorkingEmployee($query, $schedule_id)
    {
        return $query->whereDoesntHave('employeeWorkingInformations', function($query) use ($schedule_id) {
            $query->whereHas('workAddressWorkingEmployee', function($query) use ($schedule_id) {
                $query->where('planned_schedule_id', $schedule_id);
            })->where('manually_modified', true);
        });
    }

    ////////////////////////////

    /**
     * Check if this working day instance have any working information instance that is no-work one.
     *
     * @param int|boolean
     */
    public function haveANoWorkWorkingInformation()
    {
        $have_a_no_work_working_information = null;

        foreach ($this->employeeWorkingInformations as $working_info) {
            if ($working_info->isThisANoWorkWorkingInformation() === true) {
                $have_a_no_work_working_information = $working_info->id;
                break;
            }
        }

        return $have_a_no_work_working_information;
    }

}
