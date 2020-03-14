<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OptionItemRequest extends FormRequest
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
            //
            'list_work_status_default.*.name' => 'string|size:2',
            'list_work_status_default.*.status' => 'boolean',
            'list_work_status_customize.*.name' => 'string|size:2',
            'list_work_status_customize.*.status' => 'boolean',
            'list_rest_status_customize.*.name' => 'string|size:2',
            'list_rest_status_customize.*.paid_type' => 'boolean',
            'list_rest_status_customize.*.unit_type' => 'boolean',
            'list_rest_status_customize.*.status' => 'boolean',
            'list_rest_status_default.*.name' => 'string|size:2',
            'list_rest_status_default.*.status' => 'boolean',
            'list_department_status.*.status' => 'boolean',
        ];
    }
}
