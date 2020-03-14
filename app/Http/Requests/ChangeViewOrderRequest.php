<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ChangeViewOrderRequest extends FormRequest
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
            'from'                  => 'required|numeric',
            'to'                    => 'required|numeric|different:from',
            'page'                  => 'required|numeric',
            'type'                  => 'required|numeric|in:1,2',
            'current_work_location' => [
                'required_if:type,2',
                Rule::in($work_locations),
            ],
        ];
    }
}
