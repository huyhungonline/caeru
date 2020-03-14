<?php

namespace App\Http\Controllers\Attendance;

use Illuminate\Routing\Controller;
use App\EmployeeWorkingInformation;
use App\Http\Requests\EmployeeWorkingInformationRequest;
use App\Http\Controllers\Reusables\UseTheEmployeeWorkingInfoVueComponentTrait;

class EmployeeWorkingInformationController extends Controller
{
    use UseTheEmployeeWorkingInfoVueComponentTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:change_attendance_data');
    }

    /**
     * Store the new working information instance
     *
     * @param EmployeeWorkingInformationRequest      $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeWorkingInformationRequest $request)
    {
        $info = new EmployeeWorkingInformation($request->only([
            'schedule_start_work_time',
            'schedule_end_work_time',
            'schedule_break_time',
            'schedule_night_break_time',
            'schedule_working_hour',

            'planned_work_location_id',
            'planned_work_status_id',
            'planned_rest_status_id',
            'paid_rest_time_start',
            'paid_rest_time_end',
            'note',
            'planned_early_arrive_start',
            'planned_early_arrive_end',
            'planned_late_time',
            'planned_break_time',
            'real_break_time',
            'planned_night_break_time',
            'real_night_break_time',
            'planned_early_leave_time',
            'planned_overtime_start',
            'planned_overtime_end',

            'basic_salary',
            'night_salary',
            'overtime_salary',
            'deduction_salary',
            'night_deduction_salary',
            'monthly_traffic_expense',
            'daily_traffic_expense',
        ]));

        // Set the last modified person name
        $info->last_modify_person_type = EmployeeWorkingInformation::MODIFY_PERSON_TYPE_MANAGER;
        $info->last_modify_person_id = $request->user()->id;

        // All EmployeeWorkingInformation instance created by this way are considered manually modified
        $info->manually_modified = true;

        // Set the relationship with the correct EmployeeWorkingDay
        $info->employeeWorkingDay()->associate($request->input('employee_working_day_id'));

        $info->save();
        $info->load('employeeWorkingDay');

        return [
            'new_data'  => $info->necessaryDataForTheVueComponent(),
            'schedule_transfer_data' => $this->getScheduleTransferData([$info], $info->employeeWorkingDay->employee_id),
            'alert_setting_data' => $this->extractDataFromWorkLocationSetting([$info]),
            'success' => '保存しました',
        ];
    }

    /**
     * Update an EmployeeWorkingInformation instance
     *
     * @param EmployeeWorkingInformationRequest         $request
     * @param EmployeeWorkingInformation                $employee_working_info
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeWorkingInformationRequest $request, EmployeeWorkingInformation $employee_working_info)
    {
        $employee_working_info->fill($request->only([
            'planned_work_status_id',
            'planned_rest_status_id',
            'paid_rest_time_start',
            'paid_rest_time_end',
            'note',
            'planned_early_arrive_start',
            'planned_early_arrive_end',
            'planned_late_time',
            'planned_break_time',
            'real_break_time',
            'planned_night_break_time',
            'real_night_break_time',
            'planned_early_leave_time',
            'planned_overtime_start',
            'planned_overtime_end',
        ]));

        // If the user manually modified something in the schedule at the front end, then we save all those values and turn the manually_modfied flag ON
        if ($request->input('schedule_modified') === true) {

            $employee_working_info->fill($request->only([
                'schedule_start_work_time',
                'schedule_end_work_time',
                'schedule_break_time',
                'schedule_night_break_time',
                'schedule_working_hour',
                'planned_work_location_id',
            ]));
        }

        // If the user manually modified something in the salaries attributes at the front end, then we save all those values
        if ($request->input('salaries_modified') === true) {

            $salaries_data = $request->only([
                'basic_salary',
                'night_salary',
                'overtime_salary',
                'deduction_salary',
                'night_deduction_salary',
                'monthly_traffic_expense',
                'daily_traffic_expense',
            ]);

            foreach ($salaries_data as $attribute_name => $value) {
                $employee_working_info->$attribute_name = ($value !== null) ? $value : config('caeru.empty');
            }
        }

        // Set the last modified person name
        $employee_working_info->last_modify_person_type = EmployeeWorkingInformation::MODIFY_PERSON_TYPE_MANAGER;
        $employee_working_info->last_modify_person_id = $request->user()->id;

        $employee_working_info->manually_modified = true;
        $employee_working_info->temporary = false;

        $employee_working_info->save();
        $employee_working_info->load('employeeWorkingDay');

        return [
            'new_data'  => $employee_working_info->necessaryDataForTheVueComponent(),
            'schedule_transfer_data' => $this->getScheduleTransferData([$employee_working_info], $employee_working_info->employeeWorkingDay->employee_id),
            'alert_setting_data' => $this->extractDataFromWorkLocationSetting([$employee_working_info]),
            'success' => '保存しました',
        ];
    }

    /**
     * Destroy an EmployeeWorkingInformation instance
     *
     * @param EmployeeWorkingInformation        $employee_working_info
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeWorkingInformation $employee_working_info)
    {
        $employee_working_info->delete();

        return [
            'success' => '削除しました',
        ];
    }
}
