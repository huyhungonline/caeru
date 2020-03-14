<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\Reusables\ExtraValidations;
use App\Http\Requests\Reusables\PostValidationProcesses;
use Constants;


class EmployeeWorkRequest extends FormRequest
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
            'delete_card'               => 'boolean',
            'paid_holiday_exception'    => 'boolean',
            'holidays_update_day'       => 'required',
            'work_time_per_day'         => 'nullable|time',
            'work_time_change_date'     => 'nullable|date|required_with:work_time_change_to',
            'work_time_change_to'       => 'nullable|time|required_with:work_time_change_date',
            'holiday_bonus_type_extra'  => 'boolean',
            'holiday_bonus_type'        => Rule::in(array_keys(Constants::holidayBonusTypes()))
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
        ExtraValidations::dateMonthType($this, $validator, 'holidays_update_day');

        PostValidationProcesses::paidHolidayTypes($this);
    }
}
