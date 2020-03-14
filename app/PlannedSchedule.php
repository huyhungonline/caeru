<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class PlannedSchedule extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the work location
     */
    public function workLocation()
    {
        return $this->belongsTo(WorkLocation::class);
    }

    /**
     * Get the work address
     */
    public function workAddress()
    {
        return $this->belongsTo(WorkAddress::class);
    }

    //// From here on are the relationships of stage II ////
    ////////////////////////////////////////////////////////

    /**
     * Get all the employee working information currently associating with this planned schedule
     */
    public function employeeWorkingInformations()
    {
        return $this->hasMany(EmployeeWorkingInformation::class);
    }

    /**
     * Get all the work address working information currently associating with this planned schedule
     */
    public function workAddressWorkingEmployees()
    {
        return $this->hasMany(WorkAddressWorkingEmployee::class);
    }

    //////////////////////////////////////////////////////////



    /**
     * Mutator for the candidating_type
     *
     * @param string $value
     * @return void
     */
    public function setCandidatingTypeAttribute($value)
    {
        $this->attributes['candidating_type'] = ($value === 'null') ? null : $value;
    }

    /**
     * Mutator for the working_day_of_week
     *
     * @param string $value
     * @return void
     */
    public function setWorkingDaysOfWeekAttribute($value)
    {
        $this->attributes['working_days_of_week'] = implode(',', $value);
    }

    /**
     * Accessor for the working_days_of_week
     *
     * @param string $value
     * @return void
     */
    public function getWorkingDaysOfWeekAttribute($value)
    {
        return explode(',', $value);
    }

    /**
     * Get an array of chosen day(s) of week in int format. 1 (for Monday) through 7 (for Sunday)
     *
     * @return array
     */
    public function chosenDaysOfWeek()
    {
        $result = [];

        foreach ($this->working_days_of_week as $key => $value) {
            if ($value) $result[] = $key;
        }

        return $result;
    }
}
