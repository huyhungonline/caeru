<?php

namespace App;

use NationalHolidays;
use Carbon\Carbon;
use App\Company;
use App\Manager;

class EmployeeWorkingInformation extends Model
{
    /**
     * Constants for types of modify person
     */
    const MODIFY_PERSON_TYPE_MANAGER    = 1;
    const MODIFY_PERSON_TYPE_EMPLOYEE   = 2;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['date_upper_limit', 'date_of_the_upper_limit', 'last_modified_manager_name'];

    /**
     * The WorkLocations that this instance is currently affected by.
     */
    protected $affected_by_work_location = null;
    protected $affected_by_real_work_location = null;



    /**
     * Get the working day instance of this working information instance
     */
    public function employeeWorkingDay()
    {
        return $this->belongsTo(EmployeeWorkingDay::class);
    }

    /**
     * Get the planned schedule instance of this working information instance
     */
    public function plannedSchedule()
    {
        return $this->belongsTo(PlannedSchedule::class);
    }

    /**
     * Get all the modify requests for this working information instance
     */
    public function modifyRequests()
    {
        return $this->hasMany(EmployeeWorkingInformationModifyRequest::class);
    }


    /**
     * Get all the color statuses for this working information instance
     */
    public function colorStatuses()
    {
        return $this->hasMany(ColorStatus::class);
    }

    /**
     * Get all the checklist items of this working information instance
     */
    public function checklistItems()
    {
        return $this->hasMany(ChecklistItem::class);
    }

    /**
     * Get the work address working employee instance
     */
    public function workAddressWorkingEmployee()
    {
        return $this->hasOne(WorkAddressWorkingEmployee::class);
    }

    //////// Query Scope ///////

    /**
     * Get the EmployeeWorkingInformation which has not been manually modified yet
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotManuallyModified($query)
    {
        return $query->where('manually_modified', false);
    }

    /**
     * Get the EmployeeWorkingInformation which has not been concluded yet
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotConcluded($query)
    {
        return $query->whereHas('employeeWorkingDay', function($query) {
            $query->where('concluded_level_one', false)->where('concluded_level_two', false);
        });
    }

    /**
     * Get the EmployeeWorkingInformation which still does not have any WorkingTimestamp
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotHaveTimestamp($query)
    {
        return $query->whereNull('timestamped_start_work_time')->whereNull('timestamped_end_work_time');
    }

    ////////////////////////////

    //////////////////////// ACCESSORS - A WHOLE LOT OF ACCESSORS /////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////

    // 5 attributes of schedule
    // Notice: the start/end work time attributes use different function from the other's
    public function getScheduleStartWorkTimeAttribute($value)
    {
        if (isset($value)) {
            return ($value === config('caeru.empty_date')) ? null : $value;
        } else {
            $carbon_start = $this->getCarbonInstance($this->getTimeDataFromSchedules('start_work_time', null));
            return $carbon_start ? $carbon_start->format('Y-m-d H:i:s') : null;
        }
    }

    public function getScheduleEndWorkTimeAttribute($value)
    {
        if (isset($value)) {
            return ($value === config('caeru.empty_date')) ? null : $value;
        } else {
            $carbon_start = $this->getCarbonInstance($this->schedule_start_work_time);
            $carbon_end = $this->getCarbonInstance($this->getTimeDataFromSchedules('end_work_time', null));

            return $carbon_end ? $this->makeTheSecondOneBigger($carbon_start, $carbon_end)->format('Y-m-d H:i:s') : null;
        }
    }

    public function getScheduleBreakTimeAttribute($value)
    {
        if (isset($value)) {
            return ($value === config('caeru.empty')) ? null : $value;
        } else {
            return $this->genericGetFromPlannedSchedule('break_time', null);
        }
    }

    public function getScheduleNightBreakTimeAttribute($value)
    {
        if (isset($value)) {
            return ($value === config('caeru.empty')) ? null : $value;
        } else {
            return $this->genericGetFromPlannedSchedule('night_break_time', null);
        }
    }

    public function getScheduleWorkingHourAttribute($value)
    {
        if (isset($value)) {
            return ($value === config('caeru.empty_time')) ? null : $value;
        } else {
            return $this->genericGetFromPlannedSchedule('working_hour', $value);
        }
    }


    // Here come the numbers
    // These two are accessor for the paid_rest_time_start/end. We have to determine the date of each one.
    // public function getPaidRestTimeStartAttribute($value)
    // {
    //     if ($value != null) {
    //         $carbon_rest_start = $this->isWithinWorkSpan($this->getCarbonInstance($value));

    //         return $carbon_rest_start ? $carbon_rest_start->format('Y-m-d H:i:s') : null;
    //     } else {
    //         return $value;
    //     }
    // }

    // public function getPaidRestTimeEndAttribute($value)
    // {
    //     if ($value != null) {
    //         $carbon_rest_start = $this->getCarbonInstance($this->paid_rest_time_start);
    //         $carbon_rest_end = $this->getCarbonInstance($value);
    //         $carbon_rest_end = $this->isWithinWorkSpan($this->makeTheSecondOneBigger($carbon_rest_start, $carbon_rest_end));

    //         return $carbon_rest_end ? $carbon_rest_end->format('Y-m-d H:i:s') : null;
    //     } else {
    //         return $value;
    //     }
    // }


    // The locations attributes
    public function getPlannedWorkLocationIdAttribute($value)
    {
        return $this->genericGetFromPlannedSchedule('work_location_id', $value);
    }

    public function getPlannedWorkAddressIdAttribute($value)
    {
        return $this->genericGetFromPlannedSchedule('work_address_id', $value);
    }


    /**
     * If there is early_arrive_start, then it will take the value of that attribute,
     * If there is no early_arrive_start and there is a late_time then this attribute is equal to the schedule_start_work_time + the late time.
     * Else, just take the schedule_start_work_time
     *
     * The same logic apply to planned_end_work_time attribute
     */
    public function getPlannedStartWorkTimeAttribute($value)
    {
        if ($this->isPlannedWorkStatus(WorkStatus::HOUDE, WorkStatus::KYUUDE) === true) {
            return $this->planned_overtime_start;
        } elseif ($this->totalEarlyArriveTimeInMinutes('planned') > 0) {
            return !$this->isOnlyWorkingHourAndBreakTimeMode() ? $this->planned_early_arrive_start : null;
        } else {
            return $this->planned_work_span_start;
        }

    }

    /**
     * Use the start_time_round_up setting of the current planned_work_location to round up.
     */
    public function getTimestampedStartWorkTimeAttribute($value)
    {
        if ($value) {
            $round_up_by = $this->affectedByRealWorkLocation()->currentSetting()->start_time_round_up;
            $carbon_instance = $this->getCarbonInstance($value);

            $carbon_instance->minute = ceil($carbon_instance->minute/$round_up_by) * $round_up_by;
            return $carbon_instance->format('Y-m-d H:i:s');
        } else {
            return $value;
        }

    }

    /**
     * Compare the timestamped_start_work and planned_start_work, take whichever is bigger.
     */
    public function getRealStartWorkTimeAttribute($value)
    {
        if ($this->timestamped_start_work_time !== null && !$this->isPlannedWorkStatus(WorkStatus::KEKKIN, WorkStatus::FURIKYUU) && !$this->takeAWholeDayOff()) {
            $carbon_timestamped_start_work = $this->getCarbonInstance($this->timestamped_start_work_time);

            if ($this->paid_rest_time_start !== null && $this->paid_rest_time_end !== null) {
                $carbon_rest_start = $this->getCarbonInstance($this->paid_rest_time_start);
                $carbon_rest_end = $this->getCarbonInstance($this->paid_rest_time_end);

                if ($carbon_rest_start->lte($carbon_timestamped_start_work) && $carbon_timestamped_start_work->lte($carbon_rest_end)) {
                    return $carbon_rest_end->format('Y-m-d H:i:s');
                }
            }

            if ($this->planned_start_work_time !== null) {
                $carbon_planned_start_work = $this->getCarbonInstance($this->planned_start_work_time);
                return $carbon_planned_start_work->max($carbon_timestamped_start_work)->format('Y-m-d H:i:s');

            } elseif ($this->schedule_working_hour !== null) {
                if ($this->totalEarlyArriveTimeInMinutes() > 0) {
                    $carbon_early_arrive_start = $this->getCarbonInstance($this->planned_early_arrive_start);
                    return $carbon_early_arrive_start->max($carbon_timestamped_start_work)->format('Y-m-d H:i:s');

                }
                return $carbon_timestamped_start_work->format('Y-m-d H:i:s');

            } else {
                return null;
            }

        } else {
            return null;
        }
    }


    /**
     * Like planned_start_work_time_attribute, planned_end_work_time_attribute takes the overtime_start or early_leave_time into account
     */
    public function getPlannedEndWorkTimeAttribute($value)
    {
        if ($this->isPlannedWorkStatus(WorkStatus::HOUDE, WorkStatus::KYUUDE)) {
            return $this->planned_overtime_end;
        } elseif ($this->totalOvertimeInMinutes('planned') > 0) {
            return !$this->isOnlyWorkingHourAndBreakTimeMode() ? $this->planned_overtime_end : null;
        } else {
            return $this->planned_work_span_end;
        }
    }

    /**
     * Use the end_time_round_down setting of the current planned_work_location to round down.
     */
    public function getTimestampedEndWorkTimeAttribute($value)
    {
        if ($value) {
            $round_down_by = $this->affectedByRealWorkLocation()->currentSetting()->end_time_round_down;
            $carbon_instance = $this->getCarbonInstance($value);

            $carbon_instance->minute = floor($carbon_instance->minute/$round_down_by) * $round_down_by;
            return $carbon_instance->format('Y-m-d H:i:s');
        } else {
            return $value;
        }

    }

    /**
     * Compare the timestamped_end_work and planned_end_work, take whichever is smaller.
     */
    public function getRealEndWorkTimeAttribute($value)
    {
        if ($this->timestamped_end_work_time !== null && !$this->isPlannedWorkStatus(WorkStatus::KEKKIN, WorkStatus::FURIKYUU) && !$this->takeAWholeDayOff()) {
            $carbon_timestamped_end_work = $this->getCarbonInstance($this->timestamped_end_work_time);

            if ($this->paid_rest_time_start !== null && $this->paid_rest_time_end !== null) {
                $carbon_rest_start = $this->getCarbonInstance($this->paid_rest_time_start);
                $carbon_rest_end = $this->getCarbonInstance($this->paid_rest_time_end);

                if ($carbon_rest_start->lte($carbon_timestamped_end_work) && $carbon_timestamped_end_work->lte($carbon_rest_end)) {
                    return $carbon_rest_start->format('Y-m-d H:i:s');
                }
            }

            if ($this->planned_end_work_time !== null) {
                $carbon_planned_end_work = $this->getCarbonInstance($this->planned_end_work_time);
                return $carbon_planned_end_work->min($carbon_timestamped_end_work)->format('Y-m-d H:i:s');

            } elseif ($this->schedule_working_hour !== null) {

                if ($this->totalOvertimeInMinutes('planned') > 0) {
                    $carbon_overtime_end = $this->getCarbonInstance($this->planned_overtime_end);
                    return $carbon_overtime_end->min($carbon_timestamped_end_work)->format('Y-m-d H:i:s');

                } elseif ($this->estimatedEndWorkTime() !== null) {
                    return $carbon_timestamped_end_work->min($this->estimatedEndWorkTime())->format('Y-m-d H:i:s');
                }

                return $carbon_timestamped_end_work->format('Y-m-d H:i:s');
            } else {
                return null;
            }
        } else {
            return null;
        }
    }


    /**
     * The total work time of that day (exclude break, late, early leave and paid rest time)
     */
    public function getPlannedWorkingHourAttribute($value)
    {
        if (!$this->takeAWholeDayOff() && $this->remainingPlannedWorkSpanTimeInMinutes() > 0) {
            $total_planned_work_span_in_minutes = $this->convertToMinutes($this->getCarbonInstance($this->planned_work_span));

            $total_planned_work_span_in_minutes += $this->totalEarlyArriveTimeInMinutes('planned') + $this->totalOvertimeInMinutes('planned');

            return $this->minutesToString($total_planned_work_span_in_minutes);
        }
        return null;
    }

    /**
     * The total real work time of that day (exclude break, late, early leave and paid rest time)
     */
    public function getRealWorkingHourAttribute($value)
    {
        if ($this->real_start_work_time !== null && $this->real_end_work_time !== null && $this->real_work_span !== null) {
            $total_real_work_span_in_minutes = $this->convertToMinutes($this->getCarbonInstance($this->real_work_span));

            $total_real_work_span_in_minutes += $this->totalEarlyArriveTimeInMinutes('real') + $this->totalEarlyArriveTimeInMinutes('real');

            return $this->minutesToString($total_real_work_span_in_minutes);
        }
        return null;
    }


    /**
     * The planned early arrive start attribute
     */
    public function getPlannedEarlyArriveStartAttribute($value)
    {
        return $this->takeAWholeDayOff() ? null : $value;
    }

    /**
     * These two real_early_arrive_start/end attributes are calculated, base on the bigger/smaller relationship between planned_early_arrive_start/end and workStartTimestamp.
     *
     *      timestamp < start < end         OK
     *      start <= timestamp <= end       OK
     *      start < end < timestamp         Not OK
     */
    public function getRealEarlyArriveStartAttribute($value)
    {
        if ($this->planned_early_arrive_start !== null && $this->planned_early_arrive_end !== null && $this->real_start_work_time !== null && $this->real_end_work_time !== null) {

            $carbon_start = $this->getCarbonInstance($this->planned_early_arrive_start);
            $carbon_end = $this->getCarbonInstance($this->planned_early_arrive_end);

            $carbon_real = $this->getCarbonInstance($this->real_start_work_time);

            if ($carbon_real->lt($carbon_start)) {
                return $carbon_start->format('Y-m-d H:i:s');
            } elseif ($carbon_start->lte($carbon_real) && $carbon_real->lt($carbon_end)) {
                return $carbon_real->format('Y-m-d H:i:s');
            }
        }

        return null;
    }


    /**
     * Same as above of above
     */
    public function getPlannedEarlyArriveEndAttribute($value)
    {
        return $this->takeAWholeDayOff() ? null: $value;
    }

    /**
     * Same as above of above
     */
    public function getRealEarlyArriveEndAttribute($value)
    {
        if ($this->planned_early_arrive_start !== null && $this->planned_early_arrive_end !== null && $this->real_start_work_time !== null && $this->real_end_work_time !== null) {

            $carbon_end = $this->getCarbonInstance($this->planned_early_arrive_end);
            $carbon_real = $this->getCarbonInstance($this->real_start_work_time);

            if ($carbon_real->lt($carbon_end)) {
                return $carbon_end->format('Y-m-d H:i:s');
            }
        }

        return null;
    }


    /**
     * If 'take-the-whole-day-off' or the schedule of this working information is 'only-working-hour-and-break-time' mode then return null
     */
    public function getPlannedLateTimeAttribute($value)
    {
        return ($this->takeAWholeDayOff() || $this->isOnlyWorkingHourAndBreakTimeMode()) ? null : $value;
    }

    /**
     * If the timestamp_start_work_time is bigger than the planned_work_span_start then this is 'late for work' case.
     */
    public function getRealLateTimeAttribute($value)
    {
        if ($this->real_start_work_time !== null && $this->schedule_start_work_time !== null) {
            $carbon_real_start_work = $this->getCarbonInstance($this->real_start_work_time);
            $carbon_schedule_start_work = $this->getCarbonInstance($this->schedule_start_work_time);

            if ($carbon_real_start_work->gt($carbon_schedule_start_work)) {

                $late_time = $carbon_real_start_work->diffInMinutes($carbon_schedule_start_work);

                if ($this->paid_rest_time_start !== null && $this->paid_rest_time_end !== null) {
                    $carbon_rest_start = $this->getCarbonInstance($this->paid_rest_time_start);
                    $carbon_rest_end = $this->getCarbonInstance($this->paid_rest_time_end);

                    if ($carbon_rest_end->lte($carbon_real_start_work)) {
                        $late_time -= $this->totalPaidRestTimeInMinutes();
                    } elseif ($carbon_rest_start->lt($carbon_real_start_work)) {
                        $diff = $carbon_real_start_work->diffInMinutes($carbon_rest_start);
                        $late_time -= $diff;
                    }
                } elseif ($this->isPlannedRestStatus(RestStatus::ZENKYUU_1, RestStatus::ZENKYUU_2)) {
                    $late_time = $late_time - $this->totalPaidRestTimeInMinutes() - $this->schedule_break_time;
                }
                return $late_time;
            }
        }

        return null;
    }


    /**
     * The 所定内. This is actually quite an important attribute of this model.
     */
    public function getPlannedWorkSpanAttribute($value)
    {
        if ($this->takeAWholeDayOff() !== true && $this->remainingPlannedWorkSpanTimeInMinutes() > 0) {
            $total_work_span_in_minutes = $this->totalAtWorkTimeInMinutes() - $this->planned_break_time;

            // In these case, the totalAtWorkTime is still the sum of break time and working hour, but the break time is automatically set to 0, that's why we have to subtract the break time once more before calculating
            if ($this->isPlannedRestStatus(RestStatus::ZENKYUU_1, RestStatus::ZENKYUU_2, RestStatus::GOKYUU_1, RestStatus::GOKYUU_2)) {
                $total_work_span_in_minutes -= $this->schedule_break_time;
            }

            $total_work_span_in_minutes -= $this->totalPaidRestTimeInMinutes();

            if ($this->planned_late_time !== null) {
                $total_work_span_in_minutes -= $this->planned_late_time;
            }
            if ($this->planned_early_leave_time !== null) {
                $total_work_span_in_minutes -= $this->planned_early_leave_time;
            }

            return ($total_work_span_in_minutes > 0) ? $this->minutesToString($total_work_span_in_minutes) : '00:00:00';
        } else {
            return null;
        }
    }

    /**
     * Calculate the real_work_span of that day. If both real_work_span_start/end exist, then just calculate it like normal
     *              work_span = (start - end) - break
     * If both of that attributes are absent, and both timestamps for start/end_work have already existed, then we have to calculate base on
     * the real timestamp values
     *              work_span = min[((timestamp_start - timestamp_end) - break), planned_work_span]
     */
    public function getRealWorkSpanAttribute($value)
    {
        if ($this->real_start_work_time !== null && $this->real_end_work_time !== null && $this->planned_work_span !== null) {
            $carbon_real_start_work = $this->getCarbonInstance($this->real_start_work_time);
            $carbon_real_end_work = $this->getCarbonInstance($this->real_end_work_time);
            $total_work_time_in_minutes = $carbon_real_end_work->diffInMinutes($carbon_real_start_work) - $this->real_break_time;

            if ($this->paid_rest_time_start !== null && $this->paid_rest_time_end !== null) {
                $carbon_rest_start = $this->getCarbonInstance($this->paid_rest_time_start);
                $carbon_rest_end = $this->getCarbonInstance($this->paid_rest_time_end);

                if ($carbon_real_start_work->lte($carbon_rest_start) && $carbon_rest_end->lte($carbon_real_end_work)) {
                    $total_work_time_in_minutes -= $this->totalPaidRestTimeInMinutes();
                }
            }

            $carbon_planned_work_span = $this->getCarbonInstance($this->planned_work_span);
            $carbon_total_work_time = $this->getCarbonInstance($this->minutesToString($total_work_time_in_minutes));
            return $carbon_planned_work_span->min($carbon_total_work_time)->format('H:i:s');

        } else {
            return null;
        }
    }

    /**
     * Take the planned_late_time into account. Also, if the work status of this day is 前休 (or 前給), that means the employee will take the first half
     * of the day off. Because of that, we have to in crease the planned_work_span_start by half of the total work_span of that day plus the planned break time.
     */
    public function getPlannedWorkSpanStartAttribute($value)
    {
        // Check if this instance has a rest day or not
        if (!$this->takeAWholeDayOff() && $this->remainingPlannedWorkSpanTimeInMinutes() > 0) {

            $carbon_schedule_start = $this->getCarbonInstance($this->schedule_start_work_time);
            $carbon_schedule_end = $this->getCarbonInstance($this->schedule_end_work_time);

            if ($carbon_schedule_start && $carbon_schedule_end) {

                $offset = 0;
                if ($this->isPlannedRestStatus(RestStatus::ZENKYUU_1, RestStatus::ZENKYUU_2)) {
                    $offset = $this->offsetTimeToAddOrSubWhenTakeHalfDayOff();

                } elseif ($this->isRestStatusUnitDayOrHour('hour') && $this->paid_rest_time_start && $this->paid_rest_time_end) {
                    $carbon_rest_start = $this->getCarbonInstance($this->paid_rest_time_start);
                    $carbon_rest_end = $this->getCarbonInstance($this->paid_rest_time_end);
                    $diff_from_schedule_start = $carbon_rest_start->diffInMinutes($carbon_schedule_start);

                    if (!($carbon_rest_start->ne($carbon_schedule_start) && $diff_from_schedule_start > $this->planned_late_time)) {
                        $offset = $carbon_rest_end->diffInMinutes($carbon_rest_start);
                    }
                }

                if ($offset > 0) {
                    $carbon_schedule_start->addMinutes($offset);
                }
                if ($this->planned_late_time) {
                    $carbon_schedule_start->addMinutes($this->planned_late_time);
                }

                return $carbon_schedule_start->format('Y-m-d H:i:s');

            }
        }
        return null;
    }

    /**
     * In the case of 'late for work', add those late minutes to the planned_work_span_start to get the real_work_span_start
     */
    public function getRealWorkSpanStartAttribute($value)
    {
        if ($this->real_start_work_time !== null && $this->planned_work_span_start !== null) {
            $carbon_real_start = $this->getCarbonInstance($this->real_start_work_time);
            $carbon_planned_start = $this->getCarbonInstance($this->planned_work_span_start);

            return $carbon_planned_start->max($carbon_real_start)->format('Y-m-d H:i:s');
        } else {
            return null;
        }

    }

    /**
     * Have to take the early leave time into account. Also have to calculate the offset to subtract in the case of the Employee take half a day off.
     * For more detail, check the function offsetTimeToAddOrSubWhenTakeHalfDayOff
     */
    public function getPlannedWorkSpanEndAttribute($value)
    {
        // Check if this instance has a rest day or not
        if (!$this->takeAWholeDayOff() && $this->remainingPlannedWorkSpanTimeInMinutes() > 0) {

            $carbon_schedule_start = $this->getCarbonInstance($this->schedule_start_work_time);
            $carbon_schedule_end = $this->getCarbonInstance($this->schedule_end_work_time);

            if ($carbon_schedule_end) {

                $offset = 0;
                if ($this->isPlannedRestStatus(RestStatus::GOKYUU_1, RestStatus::GOKYUU_2)) {
                    $offset = $this->offsetTimeToAddOrSubWhenTakeHalfDayOff();

                } elseif ($this->isRestStatusUnitDayOrHour('hour') && $this->paid_rest_time_start && $this->paid_rest_time_end) {
                    $carbon_rest_start = $this->getCarbonInstance($this->paid_rest_time_start);
                    $carbon_rest_end = $this->getCarbonInstance($this->paid_rest_time_end);
                    $diff_from_schedule_end = $carbon_schedule_end->diffInMinutes($carbon_rest_end);

                    if (!($carbon_rest_end->ne($carbon_schedule_end) && $diff_from_schedule_end > $this->planned_early_leave_time)) {
                        $offset = $carbon_rest_end->diffInMinutes($carbon_rest_start);
                    }
                }

                if ($offset > 0) {
                    $carbon_schedule_end->subMinutes($offset);
                }
                if ($this->planned_early_leave_time) {
                    $carbon_schedule_end->subMinutes($this->planned_early_leave_time);
                }

                return $carbon_schedule_end->format('Y-m-d H:i:s');

            }
        }
        return null;
    }

    /**
     * In the case of 'leave work early', subtract those minutes to the planned_work_span_end to get the real_work_span_end
     */
    public function getRealWorkSpanEndAttribute($value)
    {
        if ($this->real_end_work_time !== null && $this->planned_work_span_end !== null) {
            $carbon_real_end = $this->getCarbonInstance($this->real_end_work_time);
            $carbon_planned_end = $this->getCarbonInstance($this->planned_work_span_end);

            return $carbon_real_end->min($carbon_planned_end)->format('Y-m-d H:i:s');
        } else {
            return null;
        }
    }


    /**
     * Planned break time and planned night break time are also delegated from the PlannedSchedule
     * While the real_break_time and real_night_break_time are delegated from the planned counter parts (conditionally though)
     */
    public function getPlannedBreakTimeAttribute($value)
    {
        if (!$this->takeAWholeDayOff() && !$this->isPlannedRestStatus(RestStatus::ZENKYUU_1, RestStatus::ZENKYUU_2, RestStatus::GOKYUU_1, RestStatus::GOKYUU_2)) {

            if ($value === null) {

                return $this->schedule_break_time;

            } else {
                return $value;
            }

        } else {
            return null;
        }
    }

    public function getRealBreakTimeAttribute($value)
    {
        if ($this->isPlannedWorkStatus(WorkStatus::KEKKIN, WorkStatus::FURIKYUU)) {
            return null;
        } elseif ($value === null) {

            if ($this->timestamped_start_work_time && $this->timestamped_end_work_time) {
                return $this->planned_break_time;
            }
        } else {
            return $value;
        }

    }

    public function getPlannedNightBreakTimeAttribute($value)
    {
        if (!$this->takeAWholeDayOff() && !$this->isPlannedRestStatus(RestStatus::ZENKYUU_1, RestStatus::ZENKYUU_2, RestStatus::GOKYUU_1, RestStatus::GOKYUU_2)) {

            if ($value === null) {

                return $this->schedule_night_break_time;

            } else {
                return ($value === config('caeru.empty')) ? null : $value;
            }

        } else {
            return null;
        }

    }

    public function getRealNightBreakTimeAttribute($value)
    {
        if ($value === null) {

            if ($this->timestamped_start_work_time && $this->timestamped_end_work_time) {
                return $this->planned_night_break_time;
            }
        } else {
            return ($value === config('caeru.empty')) ? null : $value;
        }

    }


    // This go_out_time need to be round up by the work location's setting
    public function getRealGoOutTimeAttribute($value)
    {
        if ($value) {
            $round_up_by = $this->affectedByRealWorkLocation()->currentSetting()->break_time_round_up;

            $value = ceil($value/$round_up_by) * $round_up_by;
        }
        return $value;
    }


    /**
     * If 'take-a-whole-day-off' return null
     */
    public function getPlannedEarlyLeaveTimeAttribute($value)
    {
        return !$this->takeAWholeDayOff() ? $value : null;
    }

    /**
     * If the timestamp_end_work_time is smaller than the planned_work_span_end then, this is 'leave work early' case.
     */
    public function getRealEarlyLeaveTimeAttribute($value)
    {
        if ($this->schedule_end_work_time !== null && $this->real_end_work_time !== null) {
            $carbon_schedule_end = $this->getCarbonInstance($this->schedule_end_work_time);
            $carbon_real_end = $this->getCarbonInstance($this->real_end_work_time);

            if ($carbon_real_end->lt($carbon_schedule_end)) {
                $early_leave = $carbon_schedule_end->diffInMinutes($carbon_real_end);

                if ($this->paid_rest_time_start !== null && $this->paid_rest_time_end !== null) {
                    $carbon_rest_start = $this->getCarbonInstance($this->paid_rest_time_start);
                    $carbon_rest_end = $this->getCarbonInstance($this->paid_rest_time_end);

                    if ($carbon_real_end->lte($carbon_rest_start)) {
                        $early_leave -= $this->totalPaidRestTimeInMinutes();

                    } elseif ($carbon_real_end->lt($carbon_rest_end)) {
                        $diff = $carbon_rest_end->diffInMinutes($carbon_real_end);
                        $early_leave -= $diff;
                    }

                } elseif ($this->isPlannedRestStatus(RestStatus::GOKYUU_1, RestStatus::GOKYUU_2)) {
                    $early_leave = $early_leave - $this->totalPaidRestTimeInMinutes() - $this->schedule_break_time;
                }
                return $early_leave;
            }
        } elseif ($this->real_end_work_time !== null) {
            $planned_early_leave_time = $this->planned_early_leave_time ? $this->planned_early_leave_time : 0;
            $carbon_planned_work_span = $this->getCarbonInstance($this->planned_work_span);
            $carbon_real_work_span = $this->getCarbonInstance($this->real_work_span);

            if ($carbon_planned_work_span === null && $carbon_real_work_span === null) {
                // This is the case when: work_status is houde or kyuude
                $carbon_real_end = $this->getCarbonInstance($this->real_end_work_time);
                $carbon_planned_end = $this->getCarbonInstance($this->planned_end_work_time);
                return $carbon_planned_end->eq($carbon_real_end) ? null : $carbon_planned_end->diffInMinutes($carbon_real_end);

            } elseif ($carbon_real_work_span->eq($carbon_planned_work_span)) {
                return $planned_early_leave_time;
            } else {
                return $planned_early_leave_time + $carbon_planned_work_span->diffInMinutes($carbon_real_work_span);
            }
        }

        return null;
    }

    /**
     * We have to calculate the date for these two attributes (because they can only be inputed as time_string in the form).
     */
    public function getPlannedOvertimeStartAttribute($value)
    {
        return $this->takeAWholeDayOff() ? null : $value;
    }

    /**
     * The same logic as real_early_arrive_start/end. Only the opposite.
     */
    public function getRealOvertimeStartAttribute($value)
    {
        if ($this->planned_overtime_start !== null && $this->planned_overtime_end !== null && $this->real_end_work_time !== null && $this->real_start_work_time !== null) {

            $carbon_start = $this->getCarbonInstance($this->planned_overtime_start);
            $carbon_real = $this->getCarbonInstance($this->real_end_work_time);

            if ($carbon_start->lt($carbon_real)) {
                return $carbon_start->format('Y-m-d H:i:s');
            }
        }

        return null;
    }


    /**
     * The same as above of above.
     */
    public function getPlannedOvertimeEndAttribute($value)
    {
        return $this->takeAWholeDayOff() ? null : $value;
    }

    /**
     * The same as above of above.
     */
    public function getRealOvertimeEndAttribute($value)
    {
        if ($this->planned_overtime_start !== null && $this->planned_overtime_end !== null && $this->real_end_work_time !== null && $this->real_start_work_time !== null) {

            $carbon_start = $this->getCarbonInstance($this->planned_overtime_start);
            $carbon_end = $this->getCarbonInstance($this->planned_overtime_end);

            $carbon_real = $this->getCarbonInstance($this->real_end_work_time);

            if ($carbon_real->lte($carbon_start)) {
                return null;
            } elseif ($carbon_start->lt($carbon_real) && $carbon_real->lte($carbon_end)) {
                return $carbon_real->format('Y-m-d H:i:s');
            } else {
                return $carbon_end->format('Y-m-d H:i:s');
            }
        }

        return null;
    }


    // These are the salary-related attributes
    public function getBasicSalaryAttribute($value)
    {
        $result = $this->genericGetFromPlannedSchedule('salary', $value, true);
        return ($result === config('caeru.empty')) ? null : $result;
    }

    public function getNightSalaryAttribute($value)
    {
        $result = $this->genericGetFromPlannedSchedule('night_salary', $value, true);
        return ($result === config('caeru.empty')) ? null : $result;
    }

    public function getOvertimeSalaryAttribute($value)
    {
        $result = $this->genericGetFromPlannedSchedule('overtime_salary', $value, true);
        return ($result === config('caeru.empty')) ? null : $result;
    }

    public function getDeductionSalaryAttribute($value)
    {
        $result = $this->genericGetFromPlannedSchedule('deduction_salary', $value, true);
        return ($result === config('caeru.empty')) ? null : $result;
    }

    public function getNightDeductionSalaryAttribute($value)
    {
        $result = $this->genericGetFromPlannedSchedule('night_deduction_salary', $value, true);
        return ($result === config('caeru.empty')) ? null : $result;
    }

    public function getMonthlyTrafficExpenseAttribute($value)
    {
        $result = $this->genericGetFromPlannedSchedule('monthly_traffic_expense', $value);
        return ($result === config('caeru.empty')) ? null : $result;
    }

    public function getDailyTrafficExpenseAttribute($value)
    {
        $result = $this->genericGetFromPlannedSchedule('daily_traffic_expense', $value);
        return ($result === config('caeru.empty')) ? null : $result;
    }


    // Some Virtual utility attributes
    /**
     * Base on the date_separate setting of the company to determine the uppler limit of date attributes of this working day
     *
     * @return string       a date time string with format 'Y-m-d H:i:s'
     */
    public function getDateUpperLimitAttribute()
    {
        $company = $this->employeeWorkingDay->employee->workLocation->company;

        if ($company->date_separate_type === Company::APPLY_TO_THE_DAY_BEFORE) {
            return $this->employeeWorkingDay->date . ' ' . $company->date_separate_time . ':00';
        } else {
            $day = Carbon::createFromFormat('Y-m-d', $this->employeeWorkingDay->date);
            $day->subDay();
            return $day->format('Y-m-d') . ' ' . $company->date_separate_time . ':00';
        }
    }

    /**
     * Get the day of the date_upper_limit above
     *
     * @return string    a date string with format 'Y-m-d'
     */
    public function getDateOfTheUpperLimitAttribute()
    {
        if ($this->employeeWorkingDay->employee->workLocation->company->date_separate_type === Company::APPLY_TO_THE_DAY_BEFORE) {
            return $this->employeeWorkingDay->date;
        } else {
            return Carbon::createFromFormat('Y-m-d', $this->employeeWorkingDay->date)->subDay()->format('Y-m-d');
        }
    }

    /**
     * Get the name of the manager who last modified this working information
     *
     * @return string|null
     */
    public function getLastModifiedManagerNameAttribute()
    {
        if ($this->last_modified_manager_id) {
            $manager = Manager::find($this->last_modified_manager_id);
            return $manager ? $manager->fullName() : null;
        }
        return null;
    }

    ///////////////////////////////////////////////////// End of accessors //////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /////////////////////// Utility Public Function //////////////////////////

    /**
     * An utility function to calculate the rest time of this working information instance. But the rest_status must be an customized type.
     * ( that each company creates in the page 項目設定 ).
     *
     * @param boolean       $paid   if set to true, it will calculate the paid_rest_time, and will calculate unpaid_rest_time otherwise.
     * @return string
     */
    public function customizeRestTime($paid = true)
    {
        if ($this->planned_rest_status_id) {
            $option_item = OptionItem::find($this->planned_rest_status_id);

            if ($option_item) {
                if (isset($option_item->paid_type) && ($option_item->paid_type === $paid)) {
                    if ($option_item->unit_type === true)
                        return $this->schedule_working_hour;
                    if ($option_item->unit_type === false && $this->paid_rest_time_start && $this->paid_rest_time_end) {
                        $carbon_start = $this->getCarbonInstance($this->paid_rest_time_start);
                        $carbon_end = $this->getCarbonInstance($this->paid_rest_time_end);

                        return $this->minutesToString($carbon_start->diffInMinutes($carbon_end));
                    }

                }
            }
        }

        return null;
    }

    /**
     * Get the sum of the planned/real_early_arrive time(end - start) in minutes
     *
     * @param string    $prefix     it's either 'planned' or 'real'
     * @return int
     */
    public function totalEarlyArriveTimeInMinutes($prefix = 'planned')
    {
        if ($this->{$prefix . '_early_arrive_start'} && $this->{$prefix . '_early_arrive_end'}) {
            $carbon_start = $this->getCarbonInstance($this->{$prefix . '_early_arrive_start'});
            $carbon_end = $this->getCarbonInstance($this->{$prefix . '_early_arrive_end'});

            return $carbon_end->diffInMinutes($carbon_start);
        } else {
            return 0;
        }
    }

    /**
     * Get the sum of the planned/real_overtime time(end - start) in minutes
     *
     * @param string    $prefix     it's either 'planned' or 'real'
     * @return int
     */
    public function totalOvertimeInMinutes($prefix = 'planned')
    {
        if ($this->{$prefix . '_overtime_start'} && $this->{$prefix . '_overtime_end'}) {
            $carbon_start = $this->getCarbonInstance($this->{$prefix . '_overtime_start'});
            $carbon_end = $this->getCarbonInstance($this->{$prefix . '_overtime_end'});

            return $carbon_end->diffInMinutes($carbon_start);
        } else {
            return 0;
        }
    }

    /**
     * Get the sum of the paid rest time in minutes
     *
     * @return int
     */
    public function totalPaidRestTimeInMinutes()
    {
        if ($this->isRestStatusUnitDayOrHour('hour') && $this->paid_rest_time_start && $this->paid_rest_time_end) {
            $carbon_start = $this->getCarbonInstance($this->paid_rest_time_start);
            $carbon_end = $this->getCarbonInstance($this->paid_rest_time_end);
            $total_paid_rest_time = $carbon_end->diffInMinutes($carbon_start);

            $carbon_schedule_working_hour = $this->getCarbonInstance($this->schedule_working_hour);
            $working_hour_in_minutes = $this->convertToMinutes($carbon_schedule_working_hour);

            return $total_paid_rest_time > $working_hour_in_minutes ? $working_hour_in_minutes : $total_paid_rest_time;
        } elseif ($this->isPlannedRestStatus(RestStatus::ZENKYUU_1, RestStatus::ZENKYUU_2, RestStatus::GOKYUU_1, RestStatus::GOKYUU_2)) {
            $carbon_schedule_working_hour = $this->getCarbonInstance($this->schedule_working_hour);
            return ($carbon_schedule_working_hour !== null) ? ceil($this->convertToMinutes($carbon_schedule_working_hour)/2) : null;
        } else {
            return 0;
        }
    }


    /**
     * Get the WorkLocation that this instance is being affected by
     *
     * @return WorkLocation
     */
    public function affectedByWorkLocation()
    {
        if (!$this->affected_by_work_location) {
            $this->affected_by_work_location = WorkLocation::find($this->planned_work_location_id);
        }

        return $this->affected_by_work_location;
    }

    /**
     * Get the real WorkLocation that this instance is being affected by
     *
     * @return WorkLocation
     */
    public function affectedByRealWorkLocation()
    {
        if (!$this->affected_by_real_work_location) {
            $this->affected_by_real_work_location = WorkLocation::find($this->real_work_location_id);
        }

        return $this->affected_by_real_work_location;
    }

    /**
     * Check if this working information is in the 'Only-working-hour-and-break-time mode'
     *
     * @return boolean
     */
    public function isOnlyWorkingHourAndBreakTimeMode()
    {
        return ($this->schedule_start_work_time === null) && ($this->schedule_end_work_time === null) && ($this->schedule_working_hour !== null);
    }

    /**
     * The EmployeeWorkingInformation is a gigantic model with a lot of business logic inside of it. Just serialize it normally will cost a lot,
     * because then all the attributes will be calculated completely.
     * In most pages, we only need a some attributes of that model, not all, that's why we have to choose which attribute to expose or not. Doing this will
     * reduce the processed time much more than just let laravel convert the model to json like normal.
     *
     * @return array
     */
    public function necessaryDataForTheVueComponent()
    {
        $necessary_data = [
            'id'                                =>    $this->id,
            'schedule_start_work_time'          =>    $this->schedule_start_work_time,
            'schedule_end_work_time'            =>    $this->schedule_end_work_time,
            'schedule_break_time'               =>    $this->schedule_break_time,
            'schedule_night_break_time'         =>    $this->schedule_night_break_time,
            'schedule_working_hour'             =>    $this->schedule_working_hour,

            'planned_work_status_id'            =>    $this->planned_work_status_id,
            'planned_rest_status_id'            =>    $this->planned_rest_status_id,
            'paid_rest_time_start'              =>    $this->paid_rest_time_start,
            'paid_rest_time_end'                =>    $this->paid_rest_time_end,
            'planned_work_location_id'          =>    $this->planned_work_location_id,
            'real_work_location_id'             =>    $this->real_work_location_id,
            'planned_work_address_id'           =>    $this->planned_work_address_id,
            'real_work_address_id'              =>    $this->real_work_address_id,
            'timestamped_start_work_time'       =>    $this->timestamped_start_work_time,
            'timestamped_end_work_time'         =>    $this->timestamped_end_work_time,
            'note'                              =>    $this->note,

            'real_go_out_time'                  =>    $this->real_go_out_time,
            'planned_early_arrive_start'        =>    $this->planned_early_arrive_start,
            'planned_early_arrive_end'          =>    $this->planned_early_arrive_end,
            'planned_late_time'                 =>    $this->planned_late_time,

            'planned_break_time'                =>    $this->planned_break_time,
            'real_break_time'                   =>    $this->real_break_time,
            'planned_night_break_time'          =>    $this->planned_night_break_time,
            'real_night_break_time'             =>    $this->real_night_break_time,
            'planned_early_leave_time'          =>    $this->planned_early_leave_time,
            'planned_overtime_start'            =>    $this->planned_overtime_start,
            'planned_overtime_end'              =>    $this->planned_overtime_end,
            'last_modified_manager_name'        =>    $this->last_modified_manager_name,

            'basic_salary'                      =>    $this->basic_salary,
            'night_salary'                      =>    $this->night_salary,
            'overtime_salary'                   =>    $this->overtime_salary,
            'deduction_salary'                  =>    $this->deduction_salary,
            'night_deduction_salary'            =>    $this->night_deduction_salary,
            'monthly_traffic_expense'           =>    $this->monthly_traffic_expense,
            'daily_traffic_expense'             =>    $this->daily_traffic_expense,

            'date_upper_limit'                  =>    $this->date_upper_limit,
            'date_of_the_upper_limit'           =>    $this->date_of_the_upper_limit,
        ];

        return $necessary_data;
    }


    /**
     * Determine if this instance's case is 'take a whole day off' or not.
     *
     * @return boolean
     */
    public function takeAWholeDayOff()
    {
        $carbon_schedule_working_hour = $this->getCarbonInstance($this->schedule_working_hour);
        if ($carbon_schedule_working_hour !== null) {
            $working_hour_in_minutes = $this->convertToMinutes($carbon_schedule_working_hour);

            if ($this->isRestStatusUnitDayOrHour('day') || ($this->totalPaidRestTimeInMinutes() !== 0 && $this->totalPaidRestTimeInMinutes() === $working_hour_in_minutes)) {
                return true;
            }
        }
        return false;
    }


    /**
     * Check if the rest_status of this instance belongs to the right workLocation, and if that instance's name is
     * one of the given
     *
     * @param array         the whole arguments of this function        types of the rest_statues you want to check
     * @return boolean
     */
    public function isPlannedRestStatus()
    {
        $work_location = $this->affectedByWorkLocation();

        $available_statuses = $work_location ? $work_location->activatingRestStatuses()->pluck('id')->toArray() : null;

        if ($available_statuses) {
            // The arguments of this function
            $statuses = func_get_args();

            if (count($statuses) > 0) {
                return in_array($this->planned_rest_status_id, $available_statuses) && in_array($this->planned_rest_status_id, $statuses);
            }
        }

        return null;
    }

    /**
     * Check if the work_status of this instance belongs to the right workLocation, and if that instance's name is
     * one of the given
     *
     * @param array         the whole arguments of this function         types of the work_statues you want to check
     * @return boolean
     */
    public function isPlannedWorkStatus()
    {
        $work_location = $this->affectedByWorkLocation();

        $available_statuses = $work_location ? $work_location->activatingWorkStatuses()->pluck('id')->toArray() : null;

        if ($available_statuses) {
            // The arguments of this function
            $statuses = func_get_args();

            if (count($statuses) > 0) {
                return in_array($this->planned_work_status_id, $available_statuses) && in_array($this->planned_work_status_id, $statuses);
            }
        }

        return null;

    }


    /**
     * Calculate the latest moment for this working information instance
     *
     * @return string       $moment in format 'Y-m-d H:i:s'
     */
    public function latestMomentOfThisWorkingInformation()
    {
        $the_latest_date = null;

        if ($this->planned_overtime_end !== null) {
            return $this->getCarbonInstance($this->planned_overtime_end)->format('Y-m-d') . ' 23:59:59';

        } else if ($this->planned_end_work_time !== null) {
            return $this->getCarbonInstance($this->planned_end_work_time)->format('Y-m-d') . ' 23:59:59';

        } else if ($this->schedule_end_work_time !== null){
            return $this->getCarbonInstance($this->schedule_end_work_time)->format('Y-m-d') . ' 23:59:59';

        } else {
            return $this->getCarbonInstance($this->date_upper_limit)->addDay()->format('Y-m-d') . ' 23:59:59';
        }
    }

    /**
     * Evaluate if this working information instance is a no-work one (the latest moment has been passed and still does not have any timestamp)
     *
     * @return boolean
     */
    public function isThisANoWorkWorkingInformation()
    {
        $latest_moment = $this->getCarbonInstance($this->latestMomentOfThisWorkingInformation());
        $right_now = Carbon::now();

        return $latest_moment->lt($right_now) && ($this->timestamped_start_work_time === null) && ($this->timestamped_end_work_time === null);
    }


    /**
     * Use this function to calculate the total real paid-rest-time for this instance (in days unit).
     *
     * @return float
     */
    public function totalRealPaidRestTimeInDays()
    {
        return $this->real_paid_rest_time/$this->current_work_time_per_day;
    }

    ///////////////////////////// End Utility ///////////////////////////

    //////////////////////////////////// Non-public Utility Functions /////////////////////////////////////////


    /**
     * Estimate the end work time base on the maximum working hour and those overtime fields, and also the planned early leave time.
     *
     * @return Carbon|null
     */
    protected function estimatedEndWorkTime()
    {
        if ($this->isOnlyWorkingHourAndBreakTimeMode() == true && $this->timestamped_start_work_time !== null) {
            $early_leave = ($this->planned_early_leave_time !== null) ? $this->planned_early_leave_time : 0;
            $max_working_hour_in_minutes = $this->totalAtWorkTimeInMinutes() + $this->totalOvertimeInMinutes('planned') + $this->totalEarlyArriveTimeInMinutes('planned') - $early_leave;

            if ($this->isPlannedRestStatus(RestStatus::ZENKYUU_1, RestStatus::ZENKYUU_2, RestStatus::GOKYUU_1, RestStatus::GOKYUU_2)) {
                $offset = ceil($this->convertToMinutes($this->getCarbonInstance($this->schedule_working_hour))/2);
                $max_working_hour_in_minutes -= $offset;
            }

            $carbon_timestamped_start_work = $this->getCarbonInstance($this->timestamp_start_work_time);
            $carbon_timestamped_start_work->addMinutes($max_working_hour_in_minutes);

            return $carbon_timestamped_start_work;
        } else {
            return null;
        }
    }


    /**
     * Calculate the sum of schedule working hour and schedule break time of this working information
     *
     * @return int
     */
    protected function totalAtWorkTimeInMinutes()
    {
        if ($this->schedule_break_time !== null && $this->schedule_working_hour !== null) {
            $total_time = $this->convertToMinutes($this->getCarbonInstance($this->schedule_working_hour)) + $this->schedule_break_time;

            return $total_time;
        } else {
            return 0;
        }
    }


    /**
     * Calculate the remaining time of planned_work_span in minutes
     *
     * @return int
     */
    protected function remainingPlannedWorkSpanTimeInMinutes()
    {
        $carbon_working_hour = $this->getCarbonInstance($this->schedule_working_hour);

        if ($carbon_working_hour !== null) {
            $total_working_hour = $this->convertToMinutes($carbon_working_hour);
            $late_time = ($this->planned_late_time !== null) ? $this->planned_late_time : 0;
            $early_leave = ($this->planned_early_leave_time !== null) ? $this->planned_early_leave_time : 0;

            $remaining_time = $total_working_hour - $late_time - $early_leave - $this->totalPaidRestTimeInMinutes();

            return ($remaining_time >= 0) ? $remaining_time : 0;
        } else {
            return 0;
        }
    }


    /**
     * Check if this moment is after the work span.
     * Return the correct moment, or null.
     *
     * @param Carbon    $carbon_instance
     * @return Carbon|null
     */
    protected function isAfterWorkSpan($carbon_instance)
    {
        if ($carbon_instance && $this->planned_work_span_start && $this->planned_work_span_end) {

            $carbon_work_span_end = $this->getCarbonInstance($this->planned_work_span_end);

            if ($carbon_instance->lt($carbon_work_span_end)) {
                $carbon_instance->addDay();
            }

            if ($carbon_work_span_end->lte($carbon_instance)) {
                return $carbon_instance;
            } else {
                return null;
            }

        } elseif (!$this->planned_work_span_start && !$this->planned_work_span_end && $this->planned_work_span) {
            return $carbon_instance;
        } else {
            return null;
        }
    }

    /**
     * Check if this moment is before the work span.
     * Return the correct moment, or null.
     *
     * @param Carbon    $carbon_instance
     * @return Carbon|null
     */
    protected function isBeforeWorkSpan($carbon_instance)
    {
        if ($this->planned_work_span_start && $this->planned_work_span_end) {

            $carbon_work_span_start = $this->getCarbonInstance($this->planned_work_span_start);

            if ($carbon_work_span_start->lt($carbon_instance)) {
                $carbon_instance->subDay();
            }

            if ($carbon_instance->lte($carbon_work_span_start)) {
                return $carbon_instance;
            } else {
                return null;
            }

        } elseif (!$this->planned_work_span_start && !$this->planned_work_span_end && $this->planned_work_span) {
            return $carbon_instance;
        } else {
            return null;
        }
    }

    /**
     * Check if this moment is within the work span.
     * Return the correct moment, or null.
     *
     * @param Carbon    $carbon_instance
     * @return Carbon|null
     */
    protected function isWithinWorkSpan($carbon_instance)
    {
        if ($this->planned_work_span_start && $this->planned_work_span_end) {

            $carbon_work_span_start = $this->getCarbonInstance($this->planned_work_span_start);
            $carbon_work_span_end = $this->getCarbonInstance($this->planned_work_span_end);

            if ($carbon_instance->lt($carbon_work_span_start)) {
                $carbon_instance->addDay();
            }

            if ($carbon_work_span_start->lte($carbon_instance) && $carbon_instance->lte($carbon_work_span_end)) {
                return $carbon_instance;
            } else {
                return null;
            }

        } elseif (!$this->planned_work_span_start && !$this->planned_work_span_end && $this->planned_work_span) {
            return $carbon_instance;
        } else {
            return null;
        }
    }

    /**
     * Calculate the offset time that need to be add to work_span_start time or subtract from work_span_end time (or the total work_span) in the case of
     * the Employee take half a day off.
     *
     * The offset time will be half the total work_span of that day + the break time. But if there is only work_span and work_span_start/end is null,
     * then it will be half of that work_span only.
     */
    protected function offsetTimeToAddOrSubWhenTakeHalfDayOff()
    {
        if ($this->schedule_start_work_time && $this->schedule_end_work_time) {

            $carbon_start = $this->getCarbonInstance($this->schedule_start_work_time);

            $carbon_end = $this->getCarbonInstance($this->schedule_end_work_time);

            $total_minutes = $carbon_start->diffInMinutes($carbon_end);

            // ((a + c) + c)/2 = a/2 + c. And [a/2 + c] is what we need.
            return ($total_minutes + $this->schedule_break_time)/2;

        } elseif ($this->schedule_working_hour) {

            $carbon_instance = $this->getCarbonInstance($this->schedule_working_hour);

            $carbon_zero = $this->getCarbonInstance('0:0:0');

            return ($carbon_instance->diffInMinutes($carbon_zero))/2;
        }

    }

    /**
     * Check if this instance's rest status is a day based type or an hour based type.
     *
     * @param string   $type       default is 'day', whatever else will check for hour types
     * @return boolean
     */
    protected function isRestStatusUnitDayOrHour($type = 'day')
    {
        $work_location = $this->affectedByWorkLocation();

        $available_rest_statuses = $work_location ? $work_location->activatingRestStatuses()->pluck('id')->toArray() : null;
        $current_rest_status = RestStatus::find($this->planned_rest_status_id);

        if ($available_rest_statuses && $current_rest_status) {

            if ($type === 'day')
                return in_array($this->planned_rest_status_id, $available_rest_statuses) && ($current_rest_status->unit_type == true);
            else
                return in_array($this->planned_rest_status_id, $available_rest_statuses) && ($current_rest_status->unit_type == false);

        } else {
            return false;
        }

    }


    /**
     * Use this, when there is pair of dates and you want to make sure that the end_date is bigger than the start_date.
     * In other words, this function is to correct the case start_date is late hour today and end_date is early hour tomorrow. (ex: 20:00 ~ 02:00)
     * Note: the order is very important in this function.
     *
     * @param Carbon    $start
     * @param Carbon    $end
     * @return Carbon   $end    after correct the day of this instance.
     */
    protected function makeTheSecondOneBigger(Carbon $start, Carbon $end)
    {
        return $end->lt($start) ? $end->addDay() : $end;
    }

    /**
     * Convert a Carbon instance to minutes. Only calculate from the number of hours and minutes.
     *
     * @param Carbon    $instance
     * @return int      the total minutes
     */
    protected function convertToMinutes(Carbon $instance)
    {
        return ($instance !== null) ? ($instance->hour * 60 + $instance->minute) : null;
    }

    /**
     * Calculate the different between two Cartbon instance in minutes.
     * Of course, they already have that function. This is just a wrapper to proof the case when: the end moment is smaller the start moment,
     * which means, it belongs to the next day.
     *
     * @param Carbon    $start
     * @param Carbon    $end
     * @return int      the difference in minutes
     */
    protected function calculateDiffInMinutes(Carbon $start, Carbon $end)
    {
        $end = $this->makeTheSecondOneBigger($start, $end);
        return $end->diffInMinutes($start);
    }

    /**
     * Convert a given number of minutes to a time string. Format: 'hh:mm'
     *
     * @param int       $minutes
     * @return string
     */
    protected function minutesToString($minutes)
    {
        return str_pad(floor($minutes/60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes%60, 2, '0', STR_PAD_LEFT) . ':00';
    }

    /**
     * Generate a Carbon instance from a time string with format 'hh:mm'
     *
     * @param string $time_string
     * @return Carbon
     */
    protected function getCarbonInstance($time_string)
    {
        if ($time_string != null) {

            // If the $time_string is a full-fledge time string with date parts
            if (strpos($time_string, ' ') !== false) {
                return new Carbon($time_string);
            }

            $instance = Carbon::createFromFormat('Y-m-d', $this->date_of_the_upper_limit);

            $time = explode(':', $time_string);

            $instance->hour = $time[0];
            $instance->minute = $time[1];
            if (isset($time[2])) $instance->second = $time[2];

            $carbon_date_limit = Carbon::createFromFormat('Y-m-d H:i:s', $this->date_upper_limit);
            if ($instance->lt($carbon_date_limit))
                $instance->addDay();

            return $instance;

        } else {
            return null;
        }

    }

    /**
     * The generic function, used to get the value of planned attributes
     *
     * @param $string   $field_name
     * @param mix       $value
     * @return mix
     */
    protected function genericGetFromPlannedSchedule($field_name, $value, $check_holiday = false)
    {
        // In the case the value of this field is null
        if ($value === null) {

            // Then, first, we check if this instance is connected to a PlannedSchedule in a normal way (WorkLocation case)
            if ($this->plannedSchedule) {

                // This check is for the salary fields, because the salaries are calculated base on weather this day is a national holiday or not.
                if ($check_holiday) {

                    if (NationalHolidays::is($this->employeeWorkingDay->date)) {
                        return $this->plannedSchedule->{'holiday_' . $field_name};
                    } else {
                        return $this->plannedSchedule->{'normal_' . $field_name};
                    }

                }
                return $this->plannedSchedule->$field_name;

            }
            // If not, we check if this instance is connected to a PlannedSchedule through a WorkAddressWorkingEmployee instance (WorkAddress case)
            elseif ($this->workAddressWorkingEmployee) {

                if ($this->workAddressWorkingEmployee->plannedSchedule) {

                    if ($check_holiday) {

                        if (NationalHolidays::is($this->employeeWorkingDay->date)) {
                            return $this->workAddressWorkingEmployee->plannedSchedule->{'holiday_' . $field_name};
                        } else {
                            return $this->workAddressWorkingEmployee->plannedSchedule->{'normal_' . $field_name};
                        }

                    }
                    return $this->workAddressWorkingEmployee->plannedSchedule->$field_name;
                } else {
                    return null;
                }

            } else {
                return null;
            }

        } else {
            return $value;
        }
    }

    /**
     * Why do we need this special function ?
     * Because how we retrieve planned start/end work time attributes is a little special.
     * If this instance is linked with an WorkAddressWorkingEmployee (that means the case of WorkAddress) then we have to
     * get the start/end work time from the WorkAddressWorkingInformation instance itself.
     * If this instance is linked with an PlannedSchedule (WorkLocation case), then we just get it from the PlannedSchedule instance.
     *
     * @param $string   $field_name
     * @param mix       $value
     * @return mix
     */
    protected function getTimeDataFromSchedules($field_name, $value)
    {
        // In the case the value of this field is null
        if ($value === null) {

            // Then, first, we check if this instance is connected to a PlannedSchedule in a normal way (WorkLocation case)
            if ($this->plannedSchedule) {

                return $this->plannedSchedule->$field_name;

            // If not, we check if this instance is connected to a PlannedSchedule through a WorkAddressWorkingEmployee instance (WorkAddress case)
            } elseif ($this->workAddressWorkingEmployee) {

                return $this->workAddressWorkingEmployee->workAddressWorkingInformation->{'planned_' . $field_name};

            } else {
                return null;
            }
        } else {
            return $this->$value;
        }
    }
}
