<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Reusables\TodofukenTrait;

class Company extends Model
{
    use SoftDeletes,TodofukenTrait;

    /**
     * DATE SEPARATE TYPE
     */
    // までを前日に入れる
    const APPLY_TO_THE_DAY_BEFORE    = 1;

    // から24:00までを翌日に入れる
    const APPLY_TO_THE_DAY_AFTER  = 2;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get manager list
     */
    public function managers()
    {
        return $this->hasMany(Manager::class);
    }

    /**
     * Get the work locations of this company
     */
    public function workLocations()
    {
        return $this->hasMany(WorkLocation::class);
    }

    /**
     * Get the employees of this company
     */
    public function employees()
    {
        return $this->hasManyThrough(Employee::class, WorkLocation::class);
    }

    /**
     * Get the work addresses of this company
     */
    public function workAddresses()
    {
        return $this->hasManyThrough(WorkAddress::class, WorkLocation::class);
    }

    /**
     * Get holidays
     */
    public function calendarRestDays()
    {
        return $this->hasMany(CalendarRestDay::class);
    }

    /**
     * Get total work time by months
     */
    public function calendarTotalWorkTimes()
    {
        return $this->hasMany(CalendarTotalWorkTime::class);
    }

    /**
     * Get setting
     */
    public function setting()
    {
        return $this->hasOne(Setting::class);
    }

    /**
     * Get work statuses
     */
    public function workStatuses()
    {
        return $this->hasMany(WorkStatus::class);
    }

    /**
     * Get work statuses
     */
    public function restStatuses()
    {
        return $this->hasMany(RestStatus::class);
    }/**
     * Get work statuses
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Accessor for date_separate_time field, to re-format it to hh:mm
     */
    public function getDateSeparateTimeAttribute($time)
    {
        return date('H:i', strtotime($time));
    }

}
