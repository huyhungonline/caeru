<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\WorkLocation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class EmployeeWorkingInformationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'employee_working_day_id'           => 'required|exists:employee_working_days,id',
            'schedule_modified'                 => 'boolean|required',
            'salaries_modified'                 => 'boolean|required',

            'schedule_start_work_time'          => 'sometimes|nullable|date_format:Y-m-d H:i:s',
            'schedule_end_work_time'            => 'sometimes|nullable|date_format:Y-m-d H:i:s',
            'schedule_break_time'               => 'sometimes|nullable|integer',
            'schedule_night_break_time'         => 'sometimes|nullable|integer',
            'schedule_working_hour'             => 'sometimes|nullable',

            'paid_rest_time_start'              => 'nullable|date_format:Y-m-d H:i:s',
            'paid_rest_time_end'                => 'nullable|date_format:Y-m-d H:i:s',
            'planned_early_arrive_start'        => 'nullable|date_format:Y-m-d H:i:s',
            'planned_early_arrive_end'          => 'nullable|date_format:Y-m-d H:i:s',
            'planned_late_time'                 => 'nullable|integer',
            'planned_break_time'                => 'nullable|integer',
            'real_break_time'                   => 'nullable|integer',
            'planned_night_break_time'          => 'nullable|integer',
            'real_night_break_time'             => 'nullable|integer',
            'planned_early_leave_time'          => 'nullable|integer',
            'planned_overtime_start'            => 'nullable|date_format:Y-m-d H:i:s',
            'planned_overtime_end'              => 'nullable|date_format:Y-m-d H:i:s',

            'basic_salary'                      => 'sometimes|nullable|integer',
            'night_salary'                      => 'sometimes|nullable|integer',
            'overtime_salary'                   => 'sometimes|nullable|integer',
            'deduction_salary'                  => 'sometimes|nullable|integer',
            'night_deduction_salary'            => 'sometimes|nullable|integer',
            'monthly_traffic_expense'           => 'sometimes|nullable|integer',
            'daily_traffic_expense'             => 'sometimes|nullable|integer',
        ];

    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        // Extra validation for schedule_working_hour
        $schedule_working_hour = $this->input('schedule_working_hour');
        if ($schedule_working_hour !== null && $schedule_working_hour !== config('caeru.empty_time')) {
            $validator->addRules([
                'schedule_working_hour' => 'date_format:H:i:s',
            ]);
        }

        $work_location = $this->input('planned_work_location_id');

        // If planned_work_location_id is null, then this request must be an 'update' request in which the planned_work_location_id was not changed.
        if (!$work_location) {

            $employee_working_info = $this->route('employee_working_info');
            $work_location = WorkLocation::find($employee_working_info->planned_work_location_id);

            // If at this point we still dont have a work location, that means: this is an update request from a temporary EmployeeWorkingInformation (which doestnt have
            // a planned_work_location_id from the start, because it's was created automatically in the process of error checking. (See WorkingTimestampSubscriber.php for more detail)
            if (!$work_location) {
                $validator->addRules([
                    'planned_work_location_id' => 'required',
                ]);
            }

        // Else then this request maybe a 'new' request, or an 'update' request in which, however, the planned_work_location_id was changed.
        // If that's the case, we have to validate the existence of that id.
        } else {
            $validator->addRules([
                'planned_work_location_id' => 'exists:work_locations,id',
            ]);
            $work_location = WorkLocation::find($work_location);
        }

        if ($work_location) {

            $available_work_statuses = $work_location->activatingWorkStatuses()->pluck('id')->toArray();
            $available_rest_statuses = $work_location->activatingRestStatuses()->pluck('id')->toArray();

            $validator->addRules([
                'planned_work_status_id' => [
                    'nullable',
                    Rule::in($available_work_statuses),
                ],
                'planned_rest_status_id' => [
                    'nullable',
                    Rule::in($available_rest_statuses),
                ],
            ]);
        }
    }
}
