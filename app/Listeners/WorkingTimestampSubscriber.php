<?php

namespace App\Listeners;

use App\WorkingTimestamp;
use App\WorkLocation;
use App\ChecklistItem;
use App\EmployeeWorkingInformation;
use App\Events\TimestampStartWorkError;
use App\Events\TimestampEndWorkError;
use App\Events\TimestampGoOutReturnError;
use App\Events\TimestampForgotReturnError;
use App\Events\ConfimNeededWorkWithoutScheduleError;
use App\Listeners\Reusables\RelateToChecklistErrorTrait;
use Carbon\Carbon;

class WorkingTimestampSubscriber
{
    use RelateToChecklistErrorTrait;

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\WorkingTimestampChanged',
            'App\Listeners\WorkingTimestampSubscriber@onWorkingTimestampListChanged'
        );
    }


    /**
     * Listen to the WorkingTimestampChanged event and calculate the timestamped_start_work_time, timestamped_end_work_time and also the go_out_time
     * of all EmployeeWorkingInformations of that EmployeeWorkingDay
     *
     * @param \App\Events\WorkingTimestampChanged $event
     * @return void
     */
    public function onWorkingTimestampListChanged($event)
    {
        $working_infos = $event->working_day->employeeWorkingInformations;
        $timestamps = $event->working_day->workingTimestamps()->enable()->orderBy('raw_date_time_value')->get();

        // Reset all the TimestampError records except for FORGOT_END_WORK_ERROR and FORGOT_RETURN_ERROR
        $this->resetTimestampErrorRecords($event->working_day, [ChecklistItem::FORGOT_END_WORK_ERROR, ChecklistItem::FORGOT_RETURN_ERROR]);

        // The reset of this type of error is different from the others. So it need its own reset function
        $this->resetTimestampErrorRecordsOfASpecificType($event->working_day, ChecklistItem::FORGOT_RETURN_ERROR);

        if ($working_infos->isNotEmpty()) {

            // First we reset all the timestamped_start/end_work_time, real_work_location/address_id and real_go_out_time of all the working_infos
            foreach ($working_infos as $working_info) {
                $working_info->timestamped_start_work_time = null;
                $working_info->timestamped_end_work_time = null;
                $working_info->real_work_location_id = null;
                $working_info->real_work_address_id = null;
                $working_info->real_go_out_time = null;
            }


            // Then calculate
            // $enter_work_flag = false;
            $go_out_flag = false;
            $go_out_start = null;
            // $go_out_end = null;
            $sum_go_out_time = 0;
            $current_working_info_to_which_current_go_out_time_belong = null;
            $the_odd_go_out = null;
            // $current_working_info = null;

            $last_timestamp = null;


            foreach ($timestamps as $timestamp) {

                /**
                 * If it's a start_work timestamp, we find the suitable working info (based on the schedule of that working info), then assign value for
                 * timestamped_start_work_time, real_work_location_id, real_work_address_id attributes.
                 */
                if ($timestamp->timestamped_type === WorkingTimestamp::START_WORK) {

                    // If a timestamp of anytype other than GO_OUT is at the last of the list, the forgot_return_error will be void
                    $the_odd_go_out = null;

                    // 外出後に戻り以外がある場合
                    if ($last_timestamp !== null && $last_timestamp->timestamped_type === WorkingTimestamp::GO_OUT) {
                        event(new TimestampGoOutReturnError($event->working_day));
                    }

                    $suitable_working_info = $this->getTheAvailableWorkingInfoThatHaveTheSuitablePlanForThisStartWorkTimestamp($working_infos, $timestamp);

                    // 出勤・出勤→出勤エラー
                    if ($suitable_working_info->timestamped_start_work_time !== null) {

                        event(new TimestampStartWorkError($event->working_day, $suitable_working_info->id));

                    } else {

                        $suitable_working_info->timestamped_start_work_time = $timestamp->raw_date_time_value;

                        $this->assignPlaceInfo($suitable_working_info, $timestamp);

                    }


                /**
                 * If it's a go_out_start timestamp, we find the suitable working info, if there is a current working information to which this go_out_time belong, check weather or not
                 * the suitable working info is different from the current working info. If they are the same just proceed normally. If not, reset the go_out_time ($go_out_start and $sum_go_out_time)
                 * and update the current working information, then proceed.
                 */
                } else if ($timestamp->timestamped_type === WorkingTimestamp::GO_OUT) {

                    // If a timestamp of anytype other than GO_OUT is at the last of the list, the forgot_return_error will be void
                    $the_odd_go_out = null;

                    // 外出の前が出勤または戻りでない場合
                    if (($last_timestamp !== null && $last_timestamp->timestamped_type === WorkingTimestamp::GO_OUT) || ($last_timestamp !== null && $last_timestamp->timestamped_type === WorkingTimestamp::END_WORK)) {
                        event(new TimestampGoOutReturnError($event->working_day));
                    }

                    $suitable_working_info = $this->getTheAvailableWorkingInfoThatHaveTheSuitablePlanForThisGoOutOrReturnTimestamp($working_infos, $timestamp);
                    if ($current_working_info_to_which_current_go_out_time_belong !== null) {

                        if ($current_working_info_to_which_current_go_out_time_belong->id !== $suitable_working_info->id) {
                            $current_working_info_to_which_current_go_out_time_belong = $suitable_working_info;
                            $go_out_start = $timestamp->raw_date_time_value;
                            $sum_go_out_time = 0;

                        }
                    } else {
                        $current_working_info_to_which_current_go_out_time_belong = $suitable_working_info;
                    }

                    if ($go_out_flag === false) {
                        $go_out_start = $timestamp->raw_date_time_value;
                        $go_out_flag = true;
                    }

                    $this->assignPlaceInfo($suitable_working_info, $timestamp);

                    // If this go_out timestamp is the last timestamp of this day, then the will be an odd go_out timestamp, which
                    // can be used to fire a forgot_return_error event later.
                    $the_odd_go_out = $timestamp->raw_date_time_value;

                /**
                 * If it's a return timestamp, confirm if this timestamp is within the current working information to proceed with the calculation. If proceed, calculate the go_out_time
                 * then update the real_go_out_time of that working info, then reset the $go_out_flag.
                 */
                } else if ($timestamp->timestamped_type === WorkingTimestamp::RETURN) {

                    // If a timestamp of anytype other than GO_OUT is at the last of the list, the forgot_return_error will be void
                    $the_odd_go_out = null;

                    // 戻りの前が外出以外の場合
                    if ($last_timestamp === null || $last_timestamp->timestamped_type !== WorkingTimestamp::GO_OUT) {
                        event(new TimestampGoOutReturnError($event->working_day));
                    }

                    if ($current_working_info_to_which_current_go_out_time_belong !== null &&
                        $this->isGoOutTimeWithinWorkSpanOfThisWorkInfo($current_working_info_to_which_current_go_out_time_belong, $timestamp->raw_date_time_value) &&
                        $go_out_flag === true) {

                        $carbon_go_out_start = $this->getCarbonInstance($go_out_start);
                        $carbon_go_out_end = $this->getCarbonInstance($timestamp->raw_date_time_value);
                        $sum_go_out_time += $carbon_go_out_end->diffInMinutes($carbon_go_out_start);

                        $current_working_info_to_which_current_go_out_time_belong->real_go_out_time = $sum_go_out_time;
                        $go_out_flag = false;
                    }

                /**
                 * If it's an end_work timestamp, find the suitable working info for this timestamp, assign values to the timestamped_end_work_time, work_location_id, work_address_id attributes.
                 */
                } else if ($timestamp->timestamped_type === WorkingTimestamp::END_WORK) {

                    // If a timestamp of anytype other than GO_OUT is at the last of the list, the forgot_return_error will be void
                    $the_odd_go_out = null;

                    // 外出後に戻り以外がある場合
                    if ($last_timestamp !== null && $last_timestamp->timestamped_type === WorkingTimestamp::GO_OUT) {
                        event(new TimestampGoOutReturnError($event->working_day));
                    }

                    $suitable_working_info = $this->getTheAvailableWorkingInfoThatHaveTheSuitablePlanForThisEndWorkTimestamp($working_infos, $timestamp);

                    // 退勤・退勤
                    if ($suitable_working_info->timestamped_end_work_time !== null) {

                        event(new TimestampEndWorkError($event->working_day, $suitable_working_info->id));

                    } else {

                        $suitable_working_info->timestamped_end_work_time = $timestamp->raw_date_time_value;

                        $this->assignPlaceInfo($suitable_working_info, $timestamp);

                    }

                    // 出勤が無い時に退勤を打刻した場合
                    if ($suitable_working_info->timestamped_start_work_time === null) {
                        event(new TimestampStartWorkError($event->working_day, $suitable_working_info->id));
                    }

                }

                // Assign the last timestamp
                $last_timestamp = $timestamp;
            }

            // Fire forgot_return_error event if the last timestamp is a go_out timestamp (odd go_out timestamp)
            if ($the_odd_go_out !== null) {
                event(new TimestampForgotReturnError($event->working_day, null, $the_odd_go_out));
            }

            foreach ($working_infos as $working_info) {
                $working_info->save();
            }

        // In the case of There is not any working info.
        } else {

            $temporary_working_info = new EmployeeWorkingInformation();
            $temporary_working_info->employeeWorkingDay()->associate($event->working_day);
            $temporary_working_info->temporary = true;

            $go_out_flag = false;
            $go_out_start = null;
            $sum_go_out_time = 0;

            // Assign all these timestamp to this temporary_working_info
            foreach ($timestamps as $timestamp) {

                if ($timestamp->timestamped_type === WorkingTimestamp::START_WORK && $temporary_working_info->timestamped_start_work_time === null) {

                    $temporary_working_info->timestamped_start_work_time = $timestamp->raw_date_time_value;

                    $this->assignPlaceInfo($temporary_working_info, $timestamp);


                } else if ($timestamp->timestamped_type === WorkingTimestamp::GO_OUT) {

                    if ($go_out_flag === false) {
                        $go_out_start = $timestamp->raw_date_time_value;
                        $go_out_flag = true;

                        $this->assignPlaceInfo($temporary_working_info, $timestamp);
                    }


                } else if ($timestamp->timestamped_type === WorkingTimestamp::RETURN) {

                    if ($go_out_flag === true && $go_out_start !== null) {
                        $carbon_go_out_start = $this->getCarbonInstance($go_out_start);
                        $carbon_go_out_end = $this->getCarbonInstance($timestamp->raw_date_time_value);
                        $sum_go_out_time += $carbon_go_out_end->diffInMinutes($carbon_go_out_start);

                        $temporary_working_info->real_go_out_time = $sum_go_out_time;
                        $go_out_flag = false;
                    }


                } else if ($timestamp->timestamped_type === WorkingTimestamp::END_WORK && $temporary_working_info->timestamped_end_work_time === null) {

                    $temporary_working_info->timestamped_end_work_time = $timestamp->raw_date_time_value;

                    $this->assignPlaceInfo($temporary_working_info, $timestamp);

                }
            }

            $temporary_working_info->save();

            // Finally we throw a checklist error for this
            event(new ConfimNeededWorkWithoutScheduleError($event->working_day, $temporary_working_info->id));
        }

    }


    /**
     * Find the suitable EmployeeWorkingInformation for this start_work_time timestamp
     *
     * @param Collection            $working_infos
     * @param WorkingTimestamp      $timestamp
     */
    protected function getTheAvailableWorkingInfoThatHaveTheSuitablePlanForThisStartWorkTimestamp($working_infos, $timestamp)
    {
        $current_working_info = $working_infos->first();
        $the_smallest_diff = null;

        $carbon_timestamp = $this->getCarbonInstance($timestamp->raw_date_time_value);
        foreach ($working_infos as $working_info) {

            if ($working_info->isOnlyWorkingHourAndBreakTimeMode() === true) {

                if ($working_info->timestamped_start_work_time === null) {
                    $current_working_info = $working_info;
                    break;
                }
            } else {
                if ($working_info->planned_start_work_time !== null) {
                    $carbon_instance = $this->getCarbonInstance($working_info->planned_start_work_time);
                    $current_diff = $carbon_instance->diffInMinutes($carbon_timestamp);
                    if (($the_smallest_diff === null) || (($the_smallest_diff !== null) && ($current_diff < $the_smallest_diff))) {
                        $current_working_info = $working_info;
                        $the_smallest_diff = $current_diff;
                    }
                }
            }
        }

        return $current_working_info;
    }

    /**
     * Find the suitable EmployeeWorkingInformation for this end_work_time timestamp
     *
     * @param Collection            $working_infos
     * @param WorkingTimestamp      $timestamp
     */
    protected function getTheAvailableWorkingInfoThatHaveTheSuitablePlanForThisEndWorkTimestamp($working_infos, $timestamp)
    {
        $current_working_info = $working_infos->first();
        $the_smallest_diff = null;

        $carbon_timestamp = $this->getCarbonInstance($timestamp->raw_date_time_value);
        foreach ($working_infos as $working_info) {

            if ($working_info->isOnlyWorkingHourAndBreakTimeMode() === true) {

                if ($working_info->timestamped_end_work_time === null) {
                    $current_working_info = $working_info;
                    break;
                }
            } else {
                if ($working_info->planned_end_work_time !== null) {
                    $carbon_instance = $this->getCarbonInstance($working_info->planned_end_work_time);
                    $current_diff = $carbon_instance->diffInMinutes($carbon_timestamp);
                    if (($the_smallest_diff === null) || (($the_smallest_diff !== null) && ($current_diff < $the_smallest_diff))) {
                        $current_working_info = $working_info;
                        $the_smallest_diff = $current_diff;
                    }
                }
            }
        }

        return $current_working_info;
    }

    /**
     * Find the suitable EmployeeWorkingInformation for this go_out_start / return timestamp.
     *
     * @param Collection            $working_infos
     * @param WorkingTimestamp      $timestamp
     */
    protected function getTheAvailableWorkingInfoThatHaveTheSuitablePlanForThisGoOutOrReturnTimestamp($working_infos, $timestamp)
    {
        $current_working_info = $working_infos->first();

        foreach ($working_infos as $working_info) {

            if ($working_info->isOnlyWorkingHourAndBreakTimeMode() === true) {

                if ($working_info->real_go_out_time === null) {
                    $current_working_info = $working_info;
                    break;
                }
            } else {
                if ($working_info->planned_start_work_time !== null && $working_info->planned_end_work_time !== null && $this->isGoOutTimeWithinWorkSpanOfThisWorkInfo($working_info, $timestamp->raw_date_time_value)) {
                    $current_working_info = $working_info;
                    break;
                }
            }
        }

        return $current_working_info;
    }

    /**
     * Check if a go_out timestamp is within the plan of a given EmployeeWorkingInformation.
     *
     * @param EmployeeWorkingInformation            $working_info
     * @param WorkingTimestamp                      $timestamp
     */
    protected function isGoOutTimeWithinWorkSpanOfThisWorkInfo($working_info, $go_out_timestamp)
    {
        if ($working_info->planned_start_work_time !== null && $working_info->planned_end_work_time !== null) {

            $carbon_planned_start = $this->getCarbonInstance($working_info->planned_start_work_time);
            $carbon_planned_end = $this->getCarbonInstance($working_info->planned_end_work_time);
            $carbon_go_out_timestamp = $this->getCarbonInstance($go_out_timestamp);

            return ($carbon_planned_start->lte($carbon_go_out_timestamp) && $carbon_go_out_timestamp->lte($carbon_planned_end));
        }

        // In the case there are no planned_start/end_work_time, guess we'll just return true.(because, there's the case of only-working-hour-and-break-time schedule)
        return true;
    }

    /**
     * Just a shortcut function: assign the places related information from a WorkingTimestamp instance to a EmployeeWorkingInformation instance's plan.
     *
     * @param EmployeeWorkingInformation            $working_info
     * @param WorkingTimestamp                      $timestamp
     */
    protected function assignPlaceInfo($working_info, $timestamp)
    {
        if (!$working_info->real_work_location_id) {
            $working_info->real_work_location_id = $timestamp->work_location_id;
        }
        if (!$working_info->real_work_address_id) {
            $working_info->real_work_address_id = $timestamp->work_address_id;
        }

        return $working_info;
    }

    /**
     * Initiate the Carbon instance. Just another shortcut
     *
     * @param string    $time_string
     * @return Carbon
     */
    protected function getCarbonInstance($time_string)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $time_string);
    }

    /**
     * Round the go_out_time up, base on a work_location's settings.
     *
     * @param integer   $work_location_id,
     * @param integer   $go_out_time
     * @return integer|null
     */
    protected function roundUpGoOutTimeWithSettingOfWorkLocation($work_location_id, $go_out_time)
    {
        $work_location = WorkLocation::find($work_location_id);
        $result = null;

        if ($work_location) {
            $round_up_by = $work_location->currentSetting()->break_time_round_up;

            $result = ceil($go_out_time/$round_up_by) * $round_up_by;
        }
        return $result;
    }

}