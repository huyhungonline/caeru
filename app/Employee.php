<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Reusables\PasswordTrait;
use App\Reusables\TodofukenTrait;
use App\Reusables\BelongsToWorkLocationTrait;
use Constants;
use Carbon\Carbon;

class Employee extends Model
{
    use SoftDeletes, PasswordTrait, TodofukenTrait, BelongsToWorkLocationTrait;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the work location of this employee
     */
    public function workLocation()
    {
        return $this->belongsTo(WorkLocation::class);
    }

    /**
     * Get the schedules of this employee
     */
    public function schedules()
    {
        return $this->hasMany(PlannedSchedule::class);
    }

    /**
     * Get the name of the department of this employee, if he/she has one
     */
    public function departmentName()
    {
        $department = null;
        if ($this->department_id) {
            $department = $this->workLocation->activatingDepartments()->find($this->department_id);
        }
        return $department ? $department->name : null;
    }

    /**
     * Get full name of this employee
     */
    public function fullName()
    {
        return $this->last_name . $this->first_name;
    }

    public function scheduleType()
    {
        return $this->schedule_type ? Constants::scheduleTypes()[$this->schedule_type] : null;
    }

    /**
     * Get all the subordinates of this employee, of who he/she can approve.
     */
    public function subordinates()
    {
        return $this->belongsToMany(Employee::class, 'employees_approval', 'chief_id', 'subordinate_id');
    }

    /**
     * Get all the chiefs who have the right to approve this employee
     */
    public function chiefs()
    {
        return $this->belongsToMany(Employee::class, 'employees_approval', 'subordinate_id', 'chief_id');
    }

    /**
     * Mutator for the holidays_update_day
     *
     * @param string $value
     * @return void
     */
    public function setHolidaysUpdateDayAttribute($value)
    {
        $this->attributes['holidays_update_day'] = $value ? "0004/" . $value : null;
    }

    /**
     * Accessor for the holidays_update_day
     *
     * @param string $value
     * @return void
     */
    public function getHolidaysUpdateDayAttribute($value)
    {
        return ($value) ? date('m/d', strtotime($value)) : null;
    }

    /**
     * Accessor for the work_time_per_day
     *
     * @param string $value
     * @return void
     */
    public function getWorkTimePerDayAttribute($value)
    {
        return  ($value) ? date('G:i', strtotime($value)) : null;
    }

    /**
     * Scope a query to get the working employee.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWorking($query)
    {
        return $query->where('work_status', config('constants.working'));
    }

    //// From here on are the relationships of stage II ////
    ////////////////////////////////////////////////////////

    /**
     * Get all the working day instances of this employee
     */
    public function employeeWorkingDays()
    {
        return $this->hasMany(EmployeeWorkingDay::class);
    }

    /**
     * Get all the paid holiday information instances of this employee
     */
    public function paidHolidayInformations()
    {
        return $this->hasMany(PaidHolidayInformation::class);
    }

    /**
     * Get all the checklist items of this employee
     */
    public function checklistItems()
    {
        return $this->hasMany(ChecklistItem::class);
    }

}
