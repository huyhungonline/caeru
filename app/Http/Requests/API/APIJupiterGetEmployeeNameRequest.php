<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use App\WorkLocation;
use Illuminate\Validation\Rule;

class APIJupiterGetEmployeeNameRequest extends FormRequest
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
        $list_work_location = \DB::table('work_locations')->pluck('id')->toArray();
        $list_work_address = WorkLocation::find($this->work_location_id)->workAddresses->pluck('id')->toArray();
        $card_registration_number = \DB::table('employees')->pluck('card_registration_number')->toArray();
        return [
            'work_location_id'   => [
                'required',
                'numeric',
                Rule::in($list_work_location),
            ],
            'work_address_id'   => [
                'nullable',
                'numeric',
                Rule::in($list_work_address),
            ],
            'employee_presentation_id'   => [
                'required',
                Rule::in($card_registration_number),
            ]
        ];
    }
}
