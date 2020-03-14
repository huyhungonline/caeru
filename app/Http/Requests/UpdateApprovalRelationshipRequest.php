<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Employee;

class UpdateApprovalRelationshipRequest extends FormRequest
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
        $employees = Auth::user()->company->employees->pluck('id')->toArray();

        // $chiefs_of_current = Employee::find($this->input('current'))->chiefs->pluck('id')->toArray();

        return [
            'current'   =>  [
                'required',
                'numeric',
                Rule::in($employees),
            ],
            'target'    =>  [
                'required',
                'numeric',
                Rule::in($employees),
                // Rule::notIn($chiefs_of_current),
            ],
            'status'    =>  [
                'required',
                'boolean',
            ]
        ];
    }
}
