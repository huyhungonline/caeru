<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CalendarRequest extends FormRequest
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
        $work_locations = Auth::user()->company->workLocations->pluck('id')->toArray();

        return [
            'id'                    =>  [
                'sometimes',
               Rule::in($work_locations),
            ],
            'changed_rest_days.*'   =>  'rest_day',
            'changed_work_times.*'  =>  'work_time',
        ];
    }
}
