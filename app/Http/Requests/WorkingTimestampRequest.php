<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\WorkingTimestamp;

class WorkingTimestampRequest extends FormRequest
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
        $valid_types = [
            WorkingTimestamp::START_WORK,
            WorkingTimestamp::END_WORK,
            WorkingTimestamp::GO_OUT,
            WorkingTimestamp::RETURN,
        ];

        return [
            'enable'                        => 'boolean|required',
            'processed_date_value'          => 'date|required',
            'processed_time_value'          => 'time|required',
            'timestamped_type'              => [
                'required',
                Rule::in($valid_types),
            ],
            'work_location_id'              => 'required|exists:work_locations,id',
            'work_address_id'               => 'nullable|exists:work_addresses,id',
        ];
    }
}
