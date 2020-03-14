<?php

namespace App;

class WorkAddressWorkingEmployee extends Model
{

    protected $fillable = ['work_address_working_information_id', 'employee_id', 'working_confirm', 'planned_schedule_id', 'employee_working_information_id', 'created_at'];
    /**
     * Get the work address working information instance of this work address working employee instance
     */
    public function workAddressWorkingInformation()
    {
        return $this->belongsTo(WorkAddressWorkingInformation::class);
    }

    /**
     * Get the planned schedule instance of this working information instance
     */
    public function plannedSchedule()
    {
        return $this->belongsTo(PlannedSchedule::class);
    }

    /**
     * Get the employee working information instance of this work address working employee instance
     */
    public function employeeWorkingInformation()
    {
        return $this->belongsTo(EmployeeWorkingInformation::class);
    }

    /**
     * Get the employee of this work address working employee instance
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    //////// Query Scope ///////

    /**
     * Get all the WorkAddressWorkingEmployee which does not have EmployeeWorkingInformation that has been manually modified and
     * also does not have WorkAddressWorkingInformation that has been manually modified
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotHavingManuallyModifiedWorkingInformations($query)
    {
        return $query->whereHas('employeeWorkingInformation', function($query) {
            $query->where('manually_modified', false);
        })->whereHas('workAddressWorkingInformation', function($query) {
            $query->where('manually_modified', false);
        });
    }

    /**
     * Get all the WorkAddressWorkingEmployee which does not have EmployeeWorkingInformation that has been concluded
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotHavingConcludedWorkingInformations($query)
    {
        return $query->whereHas('employeeWorkingInformation', function($query) {
            $query->whereHas('employeeWorkingDay', function($query) {
                $query->where('concluded_level_one', false)->where('concluded_level_two', false);
            });
        });
    }

    /**
     * Get all the WorkAddressWorkingEmployee which has EmployeeWorkingInformation that still does not have any WorkingTimestamp
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotHavingContainingTimestampWorkingInformation($query)
    {
        return $query->whereHas('employeeWorkingInformation', function($query) {
            $query->whereNull('timestamped_start_work_time')->whereNull('timestamped_end_work_time');
        });
    }

    ////////////////////////////
}
