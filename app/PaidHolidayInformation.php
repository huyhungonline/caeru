<?php

namespace App;
use App\WorkLocantion;

class PaidHolidayInformation extends Model
{

    /**
     * Get the employee of this paid holiday information instance
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function manager()
    {
        return $this->belongsTo(Manager::class, 'last_modified_manager_id');
    }

    /**
     * Get all the paid holiday information instances of this employee
     */
    public function changeMilestone()
    {
        return $this->hasMany(WorkTimePerDayChangeMilestone::class);
    }

    /**
     *
     * Gets available_paid_holidays attribute.
     *
     */
    public function getAvailablePaidHoliday()
    {
        return $this->getPaidHoliday($this->available_paid_holidays);
    }
    /**
     * Gets consumed_paid_holidays attribute.
     */

    public function getConsumedPaidHoliday()
    {
        return $this->getPaidHoliday($this->consumed_paid_holidays);
    }

    /**
     *
     * Gets carried_forward_paid_holidays attribute.
     *
     */
    public function getCarriedForwardPaidHoliday()
    {
        return $this->getPaidHoliday($this->carried_forward_paid_holidays);
    }

    /**
     * Convert day to day-hour-minute.
     *
     * @param float $value
     *
     * @return array.
     */
    public function getPaidHoliday($value)
    {
        $day = floor($value);
        $hour = ($value - $day)*$this->work_time_per_day;
        $hourSeprate = $hour - floor($hour);
        $minute = floor($hourSeprate * 60);
        return [
            $day,
            sprintf("%02d", floor($hour)),
            sprintf("%02d", $minute),
        ];
    }

    /**
     * revert 
     *
     * @param integer $day
     * @param integer $time
     *
     * @return float
     * 
     */
    public function revertDate($day, $time)
    {
        list($hour, $minute) = $time;
        $hourSeprate = $minute/60;
        $hour = $hourSeprate + floor($hour);
        $day = ($hour/$this->work_time_per_day) + $day;
        return $day;
    }

}
