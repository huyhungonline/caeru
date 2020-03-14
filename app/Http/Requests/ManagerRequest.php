<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\WorkLocation;
use App\Http\Requests\Reusables\PostValidationProcesses;
use App\Http\Requests\Reusables\ManagerAuthorityValidations;


class ManagerRequest extends FormRequest
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
        $work_location_list = Auth::user()->company->workLocations()->pluck('id')->toArray();

        return [
            'presentation_id'                   => 'required|max:80|regex:/^([0-9A-Za-z])*$/u',
            'first_name'                        => 'required|max:80',
            'first_name_furigana'               => 'required|max:240|regex:/^([ァ-ヾ｡-ﾟ1-9１-９、。゛゜　 ])*$/u',
            'last_name'                         => 'required|max:80',
            'last_name_furigana'                => 'required|max:240|regex:/^([ァ-ヾ｡-ﾟ1-9１-９、。゛゜　 ])*$/u',
            'telephone_1'                       => 'digits_between:0,4|required_with:telephone_2,telephone_3',
            'telephone_2'                       => 'digits_between:0,4|required_with:telephone_1,telephone_3',
            'telephone_3'                       => 'digits_between:0,5|required_with:telephone_1,telephone_2',
            'email'                             => 'nullable|email',
            'enable'                            => 'boolean',
            'company_wide_authority'            => 'boolean',
            'authorized_work_locations.*'       =>  Rule::in($work_location_list),
            'ip_address'                        => [
                'nullable',
                'regex:/^((25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])(\s)*)*$/'
            ]
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
        // When this is an update request
        $manager = $this->route('manager');
        if (is_object($manager)) {

            // If the presentation_id is changed, then it has to be unique
            if ($this->input('presentation_id') != $manager->presentation_id) {
                $validator->addRules(['presentation_id' => 'unique:managers']);
            };
            // If the password is changed, then the password_confirmation is required and need to match with password
            if (!empty($this->input('password'))) {
                $validator->addRules([
                    'password' => 'confirmed|regex:/^([0-9A-Za-z])*$/u',
                    'password_confirmation' => 'required|regex:/^([0-9A-Za-z])*$/u'
                ]);
            };

        } else {
            // When this is a make new request
            $validator->addRules([
                'presentation_id' => 'unique:managers',
                'password' => 'required|confirmed|regex:/^([0-9A-Za-z])*$/u',
                'password_confirmation' => 'required|regex:/^([0-9A-Za-z])*$/u'
            ]);
        }

        // If this manager does not have company wide authority then he should at least have authority on one work location
        $validator->sometimes('authorized_work_locations', 'required', function ($input) {
            return !$input->company_wide_authority;
        });

        // Validation for the authority part
        ManagerAuthorityValidations::addRules($validator);

        // Post validation processes
        $validator->after(function ($validator) {
            PostValidationProcesses::telephone($this);
        });
    }
}
