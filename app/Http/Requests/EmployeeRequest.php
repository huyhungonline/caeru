<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Reusables\ExtraValidations;
use App\Http\Requests\Reusables\PostValidationProcesses;
use Constants;

class EmployeeRequest extends FormRequest
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
            'presentation_id'                   => 'required|max:80',
            'first_name'                        => 'required|max:80',
            'first_name_furigana'               => 'required|max:240|regex:/^([ァ-ヾ｡-ﾟ1-9１-９、。゛゜　 ])*$/u',
            'last_name'                         => 'required|max:80',
            'last_name_furigana'                => 'required|max:240|regex:/^([ァ-ヾ｡-ﾟ1-9１-９、。゛゜　 ])*$/u',
            'birthday_1'                        => 'required|year',
            'birthday_2'                        => 'required|numeric|min:1|max:12',
            'birthday_3'                        => 'required|numeric|min:1|max:31',
            'gender'                            => [
                'required',
                Rule::in(array_keys(Constants::genders())),
            ],
            'postal_code_1'                     => 'nullable|digits:3|required_with:postal_code_2',
            'postal_code_2'                     => 'nullable|digits:4|required_with:postal_code_1',
            'todofuken'                         => 'nullable',
            'telephone_1'                       => 'digits_between:0,4|required_with:telephone_2,telephone_3',
            'telephone_2'                       => 'digits_between:0,4|required_with:telephone_1,telephone_3',
            'telephone_3'                       => 'digits_between:0,5|required_with:telephone_1,telephone_2',
            'email'                             => 'nullable|email',
            'joined_date_1'                     => 'required|year',
            'joined_date_2'                     => 'required|numeric|min:1|max:12',
            'joined_date_3'                     => 'required|numeric|min:1|max:31',
            'schedule_type'                     => [
                'required',
                Rule::in(array_keys(Constants::scheduleTypes())),
            ],
            'employment_type'                   => [
                'required',
                Rule::in(array_keys(Constants::employmentTypes())),
            ],
            'salary_type'                       => [
                'required',
                Rule::in(array_keys(Constants::salaryTypes())),
            ],
            'work_status'                       => [
                'required',
                Rule::in(array_keys(Constants::workStatuses())),
            ],
            'resigned_date_1'                   => 'nullable|year|required_with:resigned_date_2,resigned_date_3',
            'resigned_date_2'                   => 'nullable|numeric|min:1|max:12|required_with:resigned_date_1,resigned_date_3',
            'resigned_date_3'                   => 'nullable|numeric|min:1|max:31|required_with:resigned_date_2,resigned_date_1',
            'chiefs.*'                          =>  Rule::in($employees),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        // Extra validations
        ExtraValidations::todofuken($validator);
        ExtraValidations::currentWorkLocation($validator, $this->user());
        ExtraValidations::validDepartment($validator);
        ExtraValidations::threePartsDate($validator, 'birthday');
        ExtraValidations::threePartsDate($validator, 'joined_date');
        ExtraValidations::threePartsDate($validator, 'resigned_date');

        // When this is an update request
        $employee = $this->route('employee');
        if (is_object($employee)) {

            // Add one more rule to rule out the loop case of chief-subordinate relationship
            // $subordinates_of_current = $employee->subordinates->pluck('id')->toArray();
            // $validator->addRules(['chiefs.*'        => Rule::notIn($subordinates_of_current)]);

            // If the presentation_id is changed, then it has to be unique
            if ($this->input('presentation_id') != $employee->presentation_id) {
                $validator->addRules(['presentation_id' => 'unique:employees']);
            };

        } else {
            // When this is a make new request
            $validator->addRules([
                'presentation_id' => 'unique:employees',
            ]);
        }

        // Post validation process
        $validator->after(function ($validator) {
            PostValidationProcesses::telephone($this);
            PostValidationProcesses::postalCode($this);
            PostValidationProcesses::threePartsDate($this, 'birthday');
            PostValidationProcesses::threePartsDate($this, 'joined_date');
            PostValidationProcesses::threePartsDate($this, 'resigned_date');
        });
    }
}
