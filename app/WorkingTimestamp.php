<?php

namespace App;

use Carbon\Carbon;

class WorkingTimestamp extends Model
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['registerer_name', 'place_name'];

    /**
     * Constants for timestamp types
     */
    const START_WORK    = 2;
    const END_WORK      = 4;
    const GO_OUT        = 8;
    const RETURN        = 16;

    /**
     * Constant for timestamp registerer types
     */
    const MANAGER           = 11;
    const TABLET            = 12;
    const MOBILE_APP        = 13;
    const MIMAMORI_KETAI    = 14;
    // Anything else ?


    /**
     * Get the working day instance of this working timestamp instance
     */
    public function employeeWorkingDay()
    {
        return $this->belongsTo(EmployeeWorkingDay::class);
    }

    /**
     * Append this field to the seriallization of this model.
     * Get the registerer name, base on the type of the registerer.
     */
    public function getRegistererNameAttribute()
    {
        switch ($this->registerer_type) {
            case self::TABLET:
                return 'タブレット';
            case self::MOBILE_APP:
                return 'モバイル';
            case self::MIMAMORI_KETAI:
                return 'みまもり';
            case self::MANAGER:
                $manager = Manager::find($this->registerer_id);
                if ($manager) return $manager->fullName();
                else return null;
                break;
            default:
                return null;
        }
    }

    /**
     * Append this field to the serialization of this model.
     * Get the name of the place of this timestamp.
     */
    public function getPlaceNameAttribute()
    {
        if ($this->work_location_id && $work_location = WorkLocation::find($this->work_location_id)) {

            if ($this->work_address_id && $work_address = WorkAddress::find($this->work_address_id)) {
                return $work_location->name . ' ' . $work_address->name;
            }
            return $work_location->name;
        }
        return null;
    }

    /**
     * Accessor of raw_date_time_value
     */
    public function getRawDateTimeValueAttribute($value)
    {
        return ($this->timestamped_value) ? Carbon::createFromTimestamp($this->timestamped_value)->toDateTimeString() : $value ;
    }

    /**
     * Accessor of processed_date_value
     */
    public function getProcessedDateValueAttribute($value)
    {
        return ($this->raw_date_time_value) ? $this->getDateTimeRounded()->format('Y-m-d') : $value ;
    }

    /**
     * Accessor of processed_time_value
     */
    public function getProcessedTimeValueAttribute($value)
    {
        return ($this->raw_date_time_value) ? $this->getDateTimeRounded()->format('G:i') : $value ;
    }

    /**
     * Get date and time was rounded
     *
     * @return Carbon
     */
    private function getDateTimeRounded()
    {
        $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $this->raw_date_time_value);
        if ($this->timestamped_type == self::START_WORK) {

            $round_up = $this->employeeWorkingDay->employee->workLocation->currentSetting()->start_time_round_up;

            if (($round_up >= 1) && ($round_up <= 60)) {
                $minute = $carbon->minute;

                $carbon->minute = ceil($minute/$round_up) * $round_up;
            }
        } elseif ($this->timestamped_type == self::END_WORK) {

            $round_down = $this->employeeWorkingDay->employee->workLocation->currentSetting()->end_time_round_down;

            if (($round_down >= 1) && ($round_down <= 60)) {
                $minute = $carbon->minute;

                $carbon->minute = floor($minute/$round_down) * $round_down;
            }

        }

        return $carbon;
    }

    /**
     * Get all processed date time follow carbon
     *
     * @return Carbon
     */
    public function getFullProcessedDateTimeValueAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i', $this->processed_date_value . " " . $this->processed_time_value);
    }

    //////// Mutators ////////

    /**
     * Mutator for processed_time_value
     */
    public function setProcessedTimeValueAttribute($value)
    {
        $this->setRawDateTimeValue(null, $value);
    }

    /**
     * Mutator for processed_date_value
     */
    public function setProcessedDateValueAttribute($value)
    {
        $this->setRawDateTimeValue($value, null);
    }

    /**
     * Set the value for raw_date_time_value attribute. Note: this is not an actual laravel's mutator.
     *
     * @param string    $date   format 'Y-m-d'
     * @param string    $time   format 'H:i:s'
     */
    protected function setRawDateTimeValue($date = null, $time = null)
    {
        $carbon_instance = ($this->raw_date_time_value !== null) ? Carbon::createFromFormat('Y-m-d H:i:s', $this->raw_date_time_value) : Carbon::now();

        if ($date) {
            $carbon_date = new Carbon($date);
            $carbon_instance->setDate($carbon_date->year, $carbon_date->month, $carbon_date->day);
        }

        if ($time) {
            $carbon_time = new Carbon($time);
            $carbon_instance->setTime($carbon_time->hour, $carbon_time->minute, $carbon_time->second);
        }

        $this->raw_date_time_value = $carbon_instance->format('Y-m-d H:i:s');
    }

    //////// Query Scope ///////

    /**
     * Get all the 'start work' WorkingTimestamps
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStartWork($query)
    {
        return $query->where('timestamped_type', WorkingTimestamp::START_WORK);
    }

    /**
     * Get all the 'end work' WorkingTimestamps
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEndWork($query)
    {
        return $query->where('timestamped_type', WorkingTimestamp::END_WORK);
    }

    /**
     * Get all the 'go out' WorkingTimestamps
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGoOut($query)
    {
        return $query->where('timestamped_type', WorkingTimestamp::GO_OUT);
    }

    /**
     * Get all the 'return' WorkingTimestamps
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReturn($query)
    {
        return $query->where('timestamped_type', WorkingTimestamp::RETURN);
    }

    /**
     * Get all the 'enable' WorkingTimestamps
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnable($query)
    {
        return $query->where('enable', true);
    }

    ///////////////////////////

}
