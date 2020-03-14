<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\WorkLocation;
use App\WorkAddress;
use App\Employee;
use App\Http\Requests\Reusables\PostValidationProcesses;
use Constants;

class PlannedScheduleRequest extends FormRequest
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
        $work_location_list = Auth::user()->company->workLocations()->pluck('id')->toArray();
        $employee_list = Auth::user()->company->employees()->pluck('employees.id')->toArray();

        return [
            'prioritize_company_calendar'   =>  'required|boolean',
            'start_date'                    =>  'nullable|date',
            'end_date'                      =>  'nullable|date|after_or_equal:start_date',
            // 'frequency_type'                =>  Rule::in(array_keys(config('caeru.frequency_types'))),
            'working_days_of_week.*'        =>  'boolean',
            'rest_on_holiday'               =>  'boolean',
            'start_work_time'               =>  'nullable|time|required_with:work_address_id,end_work_time',
            'end_work_time'                 =>  'nullable|time|required_with:work_address_id,start_work_time',
            'break_time'                    =>  'required|numeric',
            'night_break_time'              =>  'nullable|numeric',
            'working_hour'                  =>  'required|time|working_hour',
            'work_location_id'              =>  [
                'required',
                Rule::in($work_location_list),
            ],
            'normal_salary_type'            =>  [
                'nullable',
                'required_with:normal_salary,normal_night_salary,normal_overtime_salary,normal_deduction_salary,normal_night_deduction_salary',
                Rule::in(array_keys(Constants::salaryTypes()))
            ],
            'normal_salary'                 =>  'nullable|numeric|min:0',
            'normal_night_salary'           =>  'nullable|numeric|min:0',
            'normal_overtime_salary'        =>  'nullable|numeric|min:0',
            'normal_deduction_salary'       =>  'nullable|numeric|min:0',
            'normal_night_deduction_salary' =>  'nullable|numeric|min:0',
            'holiday_salary_type'           =>  [
                'nullable',
                'required_with:holiday_salary,holiday_night_salary,holiday_overtime_salary,holiday_deduction_salary,holiday_night_deduction_salary',
                Rule::in(array_keys(Constants::salaryTypes()))
            ],
            'holiday_salary'                    =>  'nullable|numeric|min:0',
            'holiday_night_salary'              =>  'nullable|numeric|min:0',
            'holiday_overtime_salary'           =>  'nullable|numeric|min:0',
            'holiday_deduction_salary'          =>  'nullable|numeric|min:0',
            'holiday_night_deduction_salary'    =>  'nullable|numeric|min:0',
            'monthly_traffic_expense'           =>  'nullable|numeric|min:0',
            'daily_traffic_expense'             =>  'nullable|numeric|min:0',

            // Validate these fields if present in the request
            'work_address_id'                   =>  'nullable|required_with:candidating_type,candidate_number',
            'candidating_type'                  =>  'nullable|boolean|required_with:work_address_id,candidate_number',
            'candidate_number'                  =>  'nullable|numeric|required_if:candidating_type,1',
            'employee_id'                       =>  [
                'nullable',
                'required_with:candidating_type,candidate_number,work_address_id',
                Rule::in($employee_list),
            ],
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
        $work_address_list = WorkAddress::where('work_location_id', $this->input('work_location_id'))->pluck('id')->toArray();
        $validator->addRules(['work_address_id' => Rule::in($work_address_list)]);

        // Post validation processes
        $validator->after(function ($validator) {
            PostValidationProcesses::time($this, 'start_work_time');
            PostValidationProcesses::time($this, 'end_work_time');
        });
    }
}
