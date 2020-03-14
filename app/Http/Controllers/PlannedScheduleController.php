<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\PlannedSchedule as Schedule;
use App\Http\Requests\PlannedScheduleRequest as ScheduleRequest;
use App\Events\PlannedScheduleChanged;

class PlannedScheduleController extends Controller
{
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\PlannedScheduleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ScheduleRequest $request)
    {
        $toBePersistedData = [
            'employee_id',
            'work_location_id',
            'prioritize_company_calendar',
            'start_date',
            'end_date',
            'working_days_of_week',
            'rest_on_holiday',
            'start_work_time',
            'end_work_time',
            'break_time',
            'night_break_time',
            'working_hour',
            'normal_salary_type',
            'normal_salary',
            'normal_night_salary',
            'normal_overtime_salary',
            'normal_deduction_salary',
            'normal_night_deduction_salary',
            'holiday_salary_type',
            'holiday_salary',
            'holiday_night_salary',
            'holiday_overtime_salary',
            'holiday_deduction_salary',
            'holiday_night_deduction_salary',
            'monthly_traffic_expense',
            'daily_traffic_expense',
        ];

        if ($request->user()->company->use_address_system) {
            $toBePersistedData[] = 'work_address_id';
            $toBePersistedData[] = 'candidating_type';
            $toBePersistedData[] = 'candidate_number';
        }

        $schedule = new Schedule($request->only($toBePersistedData));

        $schedule->save();

        event(new PlannedScheduleChanged());

        return [
            'success'   => '保存しました',
            'id'        => $schedule->id,
        ];
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\PlannedScheduleRequest  $request
     * @param  PlannedSchedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(ScheduleRequest $request, Schedule $schedule)
    {
        $toBeUpdatedData = [
            'work_location_id',
            'prioritize_company_calendar',
            'start_date',
            'end_date',
            'working_days_of_week',
            'rest_on_holiday',
            'start_work_time',
            'end_work_time',
            'break_time',
            'night_break_time',
            'working_hour',
            'normal_salary_type',
            'normal_salary',
            'normal_night_salary',
            'normal_overtime_salary',
            'normal_deduction_salary',
            'normal_night_deduction_salary',
            'holiday_salary_type',
            'holiday_salary',
            'holiday_night_salary',
            'holiday_overtime_salary',
            'holiday_deduction_salary',
            'holiday_night_deduction_salary',
            'monthly_traffic_expense',
            'daily_traffic_expense',
        ];

        if ($request->user()->company->use_address_system) {
            $toBeUpdatedData[] = 'work_address_id';
            $toBeUpdatedData[] = 'candidating_type';
            $toBeUpdatedData[] = 'candidate_number';
        }

        $schedule->update($request->only($toBeUpdatedData));

        event(new PlannedScheduleChanged());

        return [
            'success'   => '保存しました',
            'id'        => $schedule->id,
        ];
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  PlannedSchedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        event(new PlannedScheduleChanged());

        return [
            'success' => '削除しました',
        ];
    }

}
