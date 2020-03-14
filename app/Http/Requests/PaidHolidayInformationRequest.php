<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Reusables\ExtraValidations;
use App\Http\Requests\Reusables\PostValidationProcesses;
use Constants;

class PaidHolidayInformationRequest extends FormRequest
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
            'attendance_rate'                   => ['required','max:240','regex:/^(\d+)$/'],
            'provided_paid_holidays'            => ['required','max:240','regex:/^(\d+)$/'],
            'carried_forward_day'               => ['required','max:240','regex:/^(\d+)日$/u'],
            'carried_forward_time'              =>  ['required','max:240','regex:/^(\d{1,2}):(\d{1,2})$/']
        ];
    }

}
