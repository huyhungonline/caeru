<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class APIJupiterRegisterWorkLocationRequest extends FormRequest
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
        $registration_number = \DB::table('work_locations')->pluck('registration_number')->toArray();
        return [
            'work_location_number'                       => [
                'digits:8',
                Rule::in($registration_number),
            ],
        ];
    }
}
