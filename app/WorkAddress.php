<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Reusables\TodofukenTrait;
use App\Reusables\EnableTrait;
use App\Reusables\BelongsToWorkLocationTrait;

class WorkAddress extends Model
{
    use SoftDeletes, TodofukenTrait, EnableTrait, BelongsToWorkLocationTrait;
    

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the work location of this address
     */
    public function workLocation()
    {
        return $this->belongsTo(WorkLocation::class);
    }

    /**
     * Get the schedules of this address
     */
    public function schedules()
    {
        return $this->hasMany(PlannedSchedule::class);
    }

    //// From here on are the relationships of stage II ////
    ////////////////////////////////////////////////////////

    /**
     * Get all the working day instances of this work address
     */
    public function workAddressWorkingDays()
    {
        return $this->hasMany(workAddressWorkingDay::class);
    }
}
