<?php

namespace App\Observers;

use App\ChecklistItem;
use App\WorkStatus;
use App\Setting;
use App\EmployeeWorkingInformation;
use App\Events\TimestampForgotEndWorkError;
use App\Events\ConfimNeededLateOrLeaveEarlyError;
use App\Events\ConfimNeededOffScheduleError;
use App\Events\ConfimNeededStatusMistakenError;
use App\Events\ConfimNeededOverlimitBreakError;
use App\Listeners\Reusables\RelateToChecklistErrorTrait;

class EmployeeWorkingInformationObserver
{
    use RelateToChecklistErrorTrait;

    /**
     * Listen to the updated event of the an EmployeeWorkingInformation instance to check for confirm_needed error
     *
     * @param Eloquent $model
     * @return void
     */
    public function updated(EmployeeWorkingInformation $working_info)
    {
        // Reset all confirm_needed errors except for some.
        $this->resetConfirmNeededErrorRecords($working_info, $working_info->employeeWorkingDay, [ChecklistItem::WORK_WITHOUT_SCHEDULE, ChecklistItem::HAVE_SCHEDULE_BUT_OFFLINE]);

        // The reset of this type of error is different from the others. So it need its own reset function
        $this->resetTimestampErrorRecordsOfASpecificType($working_info->employeeWorkingDay, ChecklistItem::FORGOT_END_WORK_ERROR);

        // If this EmployeeWorkingInformation was a temporary one, and this is its first time updated, reset the WORK_WITHHOUT_SCHEDULE error of this day for this employee
        if ($working_info->getOriginal('temporary') == true && $working_info->temporary == false) {
            $this->resetConfirmNeededErrorRecordsOfASpecificType($working_info, $working_info->employeeWorkingDay, ChecklistItem::WORK_WITHOUT_SCHEDULE);
        }

        // If this EmployeeWorkingInformation have any timestamp, then the HAVE_SCHEDULE_BUT_OFFLINE error will be void
        if ($working_info->timestamped_start_work_time !== null || $working_info->timestamped_end_work_time !== null) {
            $this->resetConfirmNeededErrorRecordsOfASpecificType($working_info, $working_info->employeeWorkingDay, ChecklistItem::HAVE_SCHEDULE_BUT_OFFLINE);
        }

        // ATTENTION: unlike the other blocks checking for ConfirmedNeeded errors, this block checks the TimestampError FORGOT_END_WORK_TIME
        if ($working_info->timestamped_start_work_time !== null && $working_info->timestamped_end_work_time === null) {
            event(new TimestampForgotEndWorkError($working_info->employeeWorkingDay, $working_info->id, $working_info->timestamped_start_work_time));
        }

        // 遅刻・早退 when the real_late_time is different from planned_late_time
        if (($working_info->timestamped_start_work_time !== null && $working_info->real_late_time !== $working_info->planned_late_time) ||
            ($working_info->timestamped_end_work_time !== null && $working_info->real_early_leave_time !== $working_info->planned_early_leave_time)) {
            event(new ConfimNeededLateOrLeaveEarlyError($working_info->employeeWorkingDay, $working_info->id));
        }

        // 時間外 when the real_early_arrive_start is different from planned_early_arrive_start
        if (($working_info->timestamped_start_work_time !== null && $working_info->real_early_arrive_start !== $working_info->planned_early_arrive_start) ||
            ($working_info->timestamped_end_work_time !== null && $working_info->real_overtime_end !== $working_info->planned_overtime_end)) {
            event(new ConfimNeededOffScheduleError($working_info->employeeWorkingDay, $working_info->id));
        }

        // 形態 when work_status is  furikyuu or kekkin or when the rest_status is yuukyuu or any of the whole-day rest day type or when the
        // rest_status is jiyuu and the paid_rest_time is all of the work time AND there is real_date on that instance.
        if (($working_info->isPlannedWorkStatus(WorkStatus::KEKKIN, WorkStatus::FURIKYUU) || $working_info->takeAWholeDayOff()) &&
            ($working_info->timestamped_start_work_time !== null || $working_info->timestamped_end_work_time !== null || $working_info->real_go_out_time !== null)) {
            event(new ConfimNeededStatusMistakenError($working_info->employeeWorkingDay, $working_info->id));
        }

        // 休憩・外出 if the real_work_location have setting: 'use the go_out button to calculate break time' and the real_go_out_time  bigger then the planned_break_time
        if ($work_location = $working_info->affectedByRealWorkLocation()) {
            if ($work_location->currentSetting()->go_out_button_usage === Setting::USE_AS_BREAK_TIME_BUTTON && $working_info->real_go_out_time > $working_info->planned_break_time) {
            event(new ConfimNeededOverlimitBreakError($working_info->employeeWorkingDay, $working_info->id));
            }
        }
    }

    /**
     * Listen to the deleting event of the an EmployeeWorkingInformation instance to void the HAVE_SCHEDULE_BUT_OFFLINE error
     *
     * @param Eloquent $model
     * @return void
     */
    public function deleting(EmployeeWorkingInformation $working_info)
    {
        $this->resetConfirmNeededErrorRecordsOfASpecificType($working_info, $working_info->employeeWorkingDay, ChecklistItem::HAVE_SCHEDULE_BUT_OFFLINE);
    }

}