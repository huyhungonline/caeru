<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use App\WorkLocation;
use App\WorkingTimestamp;
use Illuminate\Validation\Rule;

class APIJupiterOfflineDataRequest extends FormRequest
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
        $card_number = \DB::table('employees')->pluck('card_number')->toArray();
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
            'data.*.card_code'   => [
                'required',
                Rule::in($card_number),
            ],
            'data.*.timezone'   => 'required|timezone',
            'data.*.timestamp'   => 'required|numeric',
            'data.*.type'   => [
                'required',
                Rule::in([WorkingTimestamp::START_WORK,
                         WorkingTimestamp::END_WORK,
                         WorkingTimestamp::GO_OUT,
                         WorkingTimestamp::RETURN]),
            ],
        ];
    }
}
