<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\Reusables\ExtraValidations;
use App\Http\Requests\Reusables\PostValidationProcesses;
use App\Company;

class UpdateCompanyInfoRequest extends FormRequest
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
        // Available types for date_separate_type field
        $available_types = [
            Company::APPLY_TO_THE_DAY_BEFORE,
            Company::APPLY_TO_THE_DAY_AFTER,
        ];

        return [
            'name'                                  => 'required|max:80',
            'furigana'                              => 'required|max:240|regex:/^([ァ-ヾ｡-ﾟ1-9１-９、。゛゜　 ])*$/u',
            'postal_code_1'                         => 'nullable|digits:3|required_with:postal_code_2',
            'postal_code_2'                         => 'nullable|digits:4|required_with:postal_code_1',
            'todofuken'                             => 'nullable',
            'telephone_1'                           => 'digits_between:0,4|required_with:telephone_2,telephone_3',
            'telephone_2'                           => 'digits_between:0,4|required_with:telephone_1,telephone_3',
            'telephone_3'                           => 'digits_between:0,5|required_with:telephone_1,telephone_2',
            'fax_1'                                 => 'digits_between:0,4|required_with:fax_2,fax_3',
            'fax_2'                                 => 'digits_between:0,4|required_with:fax_1,fax_3',
            'fax_3'                                 => 'digits_between:0,5|required_with:fax_1,fax_2',
            'ceo_first_name'                        => 'max:80',
            'ceo_last_name'                         => 'max:80',
            'ceo_first_name_furigana'               => 'nullable|max:240|regex:/^([ァ-ヾ｡-ﾟ1-9１-９、。゛゜　 ])*$/u',
            'ceo_last_name_furigana'                => 'nullable|max:240|regex:/^([ァ-ヾ｡-ﾟ1-9１-９、。゛゜　 ])*$/u',
            'ceo_email'                             => 'nullable|email',
            'billing_person_first_name'             => 'max:80',
            'billing_person_last_name'              => 'max:80',
            'billing_person_first_name_furigana'    => 'nullable|max:240|regex:/^([ァ-ヾ｡-ﾟ1-9１-９、。゛゜　 ])*$/u',
            'billing_person_last_name_furigana'     => 'nullable|max:240|regex:/^([ァ-ヾ｡-ﾟ1-9１-９、。゛゜　 ])*$/u',
            'billing_person_email'                  => 'nullable|email',
            'date_separate_time'                    => 'required|time',
            'date_separate_type'                    => [
                'required',
                Rule::in($available_types),
            ],
            'use_address_system'                    => 'boolean',
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

        // Post validation processes
        $validator->after(function ($validator) {
            PostValidationProcesses::telephone($this);
            PostValidationProcesses::fax($this);
            PostValidationProcesses::postalCode($this);
            PostValidationProcesses::time($this, 'date_separate_time');
        });
    }
}
