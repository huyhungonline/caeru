<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Setting;
use Constants;

class SettingRequest extends FormRequest
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
        $timezones = \DB::table('timezones')->pluck('id')->toArray();

        return [
        'timezone'                                  => [
                'required',
                Rule::in($timezones),
            ],
        'salary_accounting_day'                     => 'required|numeric|digits_between:1,2|max:31',
        'pay_month'                                 => [
                'required',
                Rule::in([Setting::NEXT_NEXT_MONTH,
                         Setting::THIS_MONTH,
                         Setting::NEXT_MONTH]),
            ],          
        'pay_day'                                   => 'required|numeric|digits_between:1,2|max:31',
        'start_day_of_week'                         => [
                'required',
                Rule::in(array_keys(Constants::dayOfTheWeek())),
            ],
        'start_time_round_up'                       => 'required|numeric|min:0|max:60',
        'end_time_round_down'                       => 'required|numeric|min:0|max:60',
        'break_time_round_up'                       => 'required|numeric|min:0|max:60',
        'start_time_diff_limit'                      => 'required|numeric|min:0',
        'end_time_diff_limit'                      => 'required|numeric|min:0',
        'go_out_button_usage'                       => [
                'required',
                Rule::in([Setting::USE_GO_OUT_BUTTON,
                         Setting::USE_AS_BREAK_TIME_BUTTON,
                         Setting::NOT_USE_GO_OUT_BUTTON]),
            ],
        'display_go_out_time'                       => 'nullable|boolean',
        'use_overtime_button'                       => 'nullable|boolean',
        'paid_holiday_after_joined_period'          => 'nullable|numeric|digits_between:1,2|min:0|max:12',
        'paid_holiday_first_time_normal_type'       => 'nullable|numeric|min:0|max:365',
        'paid_holiday_first_time_4wdpw_type'        => 'nullable|numeric|min:0|max:365',
        'paid_holiday_first_time_3wdpw_type'        => 'nullable|numeric|min:0|max:365',
        'paid_holiday_first_time_2wdpw_type'        => 'nullable|numeric|min:0|max:365',
        'paid_holiday_first_time_1wdpw_type'        => 'nullable|numeric|min:0|max:365',
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
        $validator->after(function ($validator) {
            if (!$this->validHoliday($this->paid_holiday_increase_rate_normal_type)) {
                $validator->errors()->add('paid_holiday_increase_rate_normal_type', 'Something is wrong with this field!');
            }
            if (!$this->validHoliday($this->paid_holiday_increase_rate_4wdpw_type)) {
                $validator->errors()->add('paid_holiday_increase_rate_4wdpw_type', 'Something is wrong with this field!');
            }
            if (!$this->validHoliday($this->paid_holiday_increase_rate_3wdpw_type)) {
                $validator->errors()->add('paid_holiday_increase_rate_3wdpw_type', 'Something is wrong with this field!');
            }
            if (!$this->validHoliday($this->paid_holiday_increase_rate_2wdpw_type)) {
                $validator->errors()->add('paid_holiday_increase_rate_2wdpw_type', 'Something is wrong with this field!');
            }
            if (!$this->validHoliday($this->paid_holiday_increase_rate_1wdpw_type)) {
                $validator->errors()->add('paid_holiday_increase_rate_1wdpw_type', 'Something is wrong with this field!');
            }
        });
    }

    /**
    * The function is verify which $value of the input
    *
    * @param  $value Integer, integer, integer, integer....
    * @return boolean
    */
    public function validHoliday($value){
        $trimValues = str_replace(" ","",$value);
        $trimValue = explode(",", $trimValues);
        for ($i=0; $i < count($trimValue); $i++) { 
            $pattern = '/^[0-9 ]{0,3}$/';
            if (preg_match ($pattern, $trimValue[$i]))
            {
               if ($trimValue[$i] > 365 ) {
                    return false;
                }
                if (count( array_keys( $trimValue, "$trimValue[$i]" )) > 1) {
                   return false;
                }
            }else{
                return false;
            }
        }
        return true;
    }
}