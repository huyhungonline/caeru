<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MoveApprovalRelationshipRequest extends FormRequest
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

        return [
            'current'   =>  [
                'required',
                'numeric',
                Rule::in($employees),
            ],
            'new'       =>  [
                'required',
                'numeric',
                Rule::in($employees),
            ]
        ];
    }
}
