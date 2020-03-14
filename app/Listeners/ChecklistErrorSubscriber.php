<?php

namespace App\Listeners;

use App\ChecklistItem;
use App\ChecklistErrorTimer;
use App\Events\TimestampStartWorkError;
use App\Events\TimestampEndWorkError;
use App\Events\TimestampGoOutReturnError;
use App\Events\TimestampForgotEndWorkError;
use App\Events\TimestampForgotReturnError;
use App\Events\ConfimNeededLateOrLeaveEarlyError;
use App\Events\ConfimNeededOffScheduleError;
use App\Events\ConfimNeededStatusMistakenError;
use App\Events\ConfimNeededOverlimitBreakError;
use App\Events\ConfimNeededWorkWithoutScheduleError;
use App\Events\ConfimNeededHaveScheduleButOfflineError;
use Carbon\Carbon;

class ChecklistErrorSubscriber
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            TimestampStartWorkError::class,
            'App\Listeners\ChecklistErrorSubscriber@onStartWorkError'
        );

        $events->listen(
            TimestampEndWorkError::class,
            'App\Listeners\ChecklistErrorSubscriber@onEndWorkError'
        );

        $events->listen(
            TimestampGoOutReturnError::class,
            'App\Listeners\ChecklistErrorSubscriber@onGoOutReturnError'
        );

        $events->listen(
            TimestampForgotEndWorkError::class,
            'App\Listeners\ChecklistErrorSubscriber@onForgotEndWorkError'
        );

        $events->listen(
            TimestampForgotReturnError::class,
            'App\Listeners\ChecklistErrorSubscriber@onForgotReturnError'
        );

        $events->listen(
            ConfimNeededLateOrLeaveEarlyError::class,
            'App\Listeners\ChecklistErrorSubscriber@onLateOrLeaveEarlyError'
        );

        $events->listen(
            ConfimNeededOffScheduleError::class,
            'App\Listeners\ChecklistErrorSubscriber@onOffScheduleError'
        );

        $events->listen(
            ConfimNeededStatusMistakenError::class,
            'App\Listeners\ChecklistErrorSubscriber@onStatusMistakenError'
        );

        $events->listen(
            ConfimNeededOverlimitBreakError::class,
            'App\Listeners\ChecklistErrorSubscriber@onOverlimitBreakError'
        );

        $events->listen(
            ConfimNeededWorkWithoutScheduleError::class,
            'App\Listeners\ChecklistErrorSubscriber@onWorkWithoutScheduleError'
        );

        $events->listen(
            ConfimNeededHaveScheduleButOfflineError::class,
            'App\Listeners\ChecklistErrorSubscriber@onHaveScheduleButOfflineError'
        );
    }


    /**
     * When a TimestampError is detected, create a checklist entry (if it doestnt exist) for that error.
     *
     * @param TimestampStartWorkError   $event
     */
    public function onStartWorkError(TimestampStartWorkError $event)
    {
        $checklist_item = ChecklistItem::firstOrCreate([
            'date'                              => $event->working_day->date,
            'employee_id'                       => $event->working_day->employee_id,
            'item_type'                         => ChecklistItem::TIMESTAMP_ERROR,
            'error_type'                        => ChecklistItem::START_WORK_ERROR,
            'employee_working_information_id'   => $event->working_info_id,
        ]);
    }

    /**
     * When a TimestampError is detected, create a checklist entry (if it doestnt exist) for that error.
     *
     * @param TimestampEndWorkError   $event
     */
    public function onEndWorkError(TimestampEndWorkError $event)
    {
        $checklist_item = ChecklistItem::firstOrCreate([
            'date'                              => $event->working_day->date,
            'employee_id'                       => $event->working_day->employee_id,
            'item_type'                         => ChecklistItem::TIMESTAMP_ERROR,
            'error_type'                        => ChecklistItem::END_WORK_ERROR,
            'employee_working_information_id'   => $event->working_info_id,
        ]);
    }

    /**
     * When a TimestampError is detected, create a checklist entry (if it doestnt exist) for that error.
     *
     * @param TimestampGoOutReturnError   $event
     */
    public function onGoOutReturnError(TimestampGoOutReturnError $event)
    {
        $checklist_item = ChecklistItem::firstOrCreate([
            'date'                              => $event->working_day->date,
            'employee_id'                       => $event->working_day->employee_id,
            'item_type'                         => ChecklistItem::TIMESTAMP_ERROR,
            'error_type'                        => ChecklistItem::GO_OUT_RETURN_ERROR,
            'employee_working_information_id'   => $event->working_info_id,
        ]);
    }

    /**
     * With this error, we have to check if the error has passed the due_time or not. If it has already pass the due_date.
     * Create checklist entry for that error, or else, create a timer entry for that error.
     *
     * @param TimestampForgotEndWorkError   $event
     */
    public function onForgotEndWorkError(TimestampForgotEndWorkError $event)
    {
        // Get the current time exact to the minutes.
        $right_now = Carbon::now();
        $right_now->second = 0;

        $due_time = Carbon::createFromFormat('Y-m-d H:i:s', $event->start_work_timestamp)->addDay();

        // If this error has already passed due time, just directly create an ChecklistError entry for it
        if ($right_now->gt($due_time)) {
            $checklist_item = ChecklistItem::firstOrCreate([
                'date'                              => $event->working_day->date,
                'employee_id'                       => $event->working_day->employee_id,
                'item_type'                         => ChecklistItem::TIMESTAMP_ERROR,
                'error_type'                        => ChecklistItem::FORGOT_END_WORK_ERROR,
                'employee_working_information_id'   => $event->working_info_id,
            ]);

        } else {

            // Otherwise create a timer, so that the ChecklistError entry can be created 24 hours later
            ChecklistErrorTimer::firstOrCreate([
                'company_code'                      => $event->working_day->employee->workLocation->company->code,
                'employee_id'                       => $event->working_day->employee->id,
                'date'                              => $event->working_day->date,
                'timestamp_error_type'              => ChecklistItem::FORGOT_END_WORK_ERROR,
                'employee_working_information_id'   => $event->working_info_id,
                'due_time'                          => $due_time,
            ]);
        }

    }

    /**
     * With this error, we have to check if the error has passed the due_time or not. If it has already pass the due_date.
     * Create checklist entry for that error, or else, create a timer entry for that error.
     *
     * @param TimestampForgotReturnError   $event
     */
    public function onForgotReturnError(TimestampForgotReturnError $event)
    {
        // Get the current time exact to the minutes.
        $right_now = Carbon::now();
        $right_now->second = 0;

        $due_time = Carbon::createFromFormat('Y-m-d H:i:s', $event->go_out_timestamp)->addDay();

        // If this error has already passed due time, just directly create an ChecklistError entry for it
        if ($right_now->gt($due_time)) {
            $checklist_item = ChecklistItem::firstOrCreate([
                'date'                              => $event->working_day->date,
                'employee_id'                       => $event->working_day->employee_id,
                'item_type'                         => ChecklistItem::TIMESTAMP_ERROR,
                'error_type'                        => ChecklistItem::FORGOT_RETURN_ERROR,
                'employee_working_information_id'   => $event->working_info_id,
            ]);

        } else {

            // Otherwise create a timer, so that the ChecklistError entry can be created 24 hours later
            ChecklistErrorTimer::firstOrCreate([
                'company_code'                      => $event->working_day->employee->workLocation->company->code,
                'employee_id'                       => $event->working_day->employee->id,
                'date'                              => $event->working_day->date,
                'timestamp_error_type'              => ChecklistItem::FORGOT_RETURN_ERROR,
                'employee_working_information_id'   => $event->working_info_id,
                'due_time'                          => $due_time,
            ]);
        }
    }

    /**
     * When a ConfirmNeeded error is detected, create a checklist entry (if it doestnt exist) for that error.
     *
     * @param ConfimNeededLateOrLeaveEarlyError   $event
     */
    public function onLateOrLeaveEarlyError(ConfimNeededLateOrLeaveEarlyError $event)
    {
        $checklist_item = ChecklistItem::firstOrCreate([
            'date'                              => $event->working_day->date,
            'employee_id'                       => $event->working_day->employee_id,
            'item_type'                         => ChecklistItem::CONFIRM_NEEDED,
            'error_type'                        => ChecklistItem::LATE_OR_LEAVE_EARLY_TYPE,
            'employee_working_information_id'   => $event->working_info_id,
        ]);
    }

    /**
     * When a ConfirmNeeded error is detected, create a checklist entry (if it doestnt exist) for that error.
     *
     * @param ConfimNeededOffScheduleError   $event
     */
    public function onOffScheduleError(ConfimNeededOffScheduleError $event)
    {
        $checklist_item = ChecklistItem::firstOrCreate([
            'date'                              => $event->working_day->date,
            'employee_id'                       => $event->working_day->employee_id,
            'item_type'                         => ChecklistItem::CONFIRM_NEEDED,
            'error_type'                        => ChecklistItem::OFF_SCHEDULE_TIME,
            'employee_working_information_id'   => $event->working_info_id,
        ]);
    }

    /**
     * When a ConfirmNeeded error is detected, create a checklist entry (if it doestnt exist) for that error.
     *
     * @param ConfimNeededStatusMistakenError   $event
     */
    public function onStatusMistakenError(ConfimNeededStatusMistakenError $event)
    {
        $checklist_item = ChecklistItem::firstOrCreate([
            'date'                              > $event->working_day->date,
            'employee_id'                       => $event->working_day->employee_id,
            'item_type'                         => ChecklistItem::CONFIRM_NEEDED,
            'error_type'                        => ChecklistItem::STATUS_MISTAKEN,
            'employee_working_information_id'   => $event->working_info_id,
        ]);
    }

    /**
     * When a ConfirmNeeded error is detected, create a checklist entry (if it doestnt exist) for that error.
     *
     * @param ConfimNeededOverlimitBreakError   $event
     */
    public function onOverlimitBreakError(ConfimNeededOverlimitBreakError $event)
    {
        $checklist_item = ChecklistItem::firstOrCreate([
            'date'                              => $event->working_day->date,
            'employee_id'                       => $event->working_day->employee_id,
            'item_type'                         => ChecklistItem::CONFIRM_NEEDED,
            'error_type'                        => ChecklistItem::OVERLIMIT_BREAK_TIME,
            'employee_working_information_id'   => $event->working_info_id,
        ]);
    }

    /**
     * When a ConfirmNeeded error is detected, create a checklist entry (if it doestnt exist) for that error.
     *
     * @param ConfimNeededWorkWithoutScheduleError   $event
     */
    public function onWorkWithoutScheduleError(ConfimNeededWorkWithoutScheduleError $event)
    {
        $checklist_item = ChecklistItem::firstOrCreate([
            'date'                              => $event->working_day->date,
            'employee_id'                       => $event->working_day->employee_id,
            'item_type'                         => ChecklistItem::CONFIRM_NEEDED,
            'error_type'                        => ChecklistItem::WORK_WITHOUT_SCHEDULE,
            'employee_working_information_id'   => $event->working_info_id,
        ]);
    }

    /**
     * When a ConfirmNeeded error is detected, create a checklist entry (if it doestnt exist) for that error.
     *
     * @param ConfimNeededHaveScheduleButOfflineError   $event
     */
    public function onHaveScheduleButOfflineError(ConfimNeededHaveScheduleButOfflineError $event)
    {
        $checklist_item = ChecklistItem::firstOrCreate([
            'date'                              => $event->working_day->date,
            'employee_id'                       => $event->working_day->employee_id,
            'item_type'                         => ChecklistItem::CONFIRM_NEEDED,
            'error_type'                        => ChecklistItem::HAVE_SCHEDULE_BUT_OFFLINE,
            'employee_working_information_id'   => $event->working_info_id,
        ]);
    }
}
