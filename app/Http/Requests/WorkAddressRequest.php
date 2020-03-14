<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Reusables\ExtraValidations;
use App\Http\Requests\Reusables\PostValidationProcesses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WorkAddressRequest extends FormRequest
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
        if (Auth::user()->company_wide_authority)
            $work_location_list = Auth::user()->company->workLocations->pluck('id')->toArray();
        else
            $work_location_list = Auth::user()->workLocations->pluck('id')->toArray();

        return [
            'presentation_id'                       => 'required|max:20',
            'name'                                  => 'required|max:80',
            'furigana'                              => 'required|nullable|max:240|regex:/^([ァ-ヾ｡-ﾟ1-9１-９、。゛゜　 ])*$/u',
            'postal_code_1'                         => 'nullable|digits:3|required_with:postal_code_2',
            'postal_code_2'                         => 'nullable|digits:4|required_with:postal_code_1',
            'todofuken'                             => 'nullable',
            'login_range'                           => 'nullable|numeric|min:0|required_with:longitude,latitude',
            'latitude'                              => 'nullable|numeric|min:-90|max:90|required_with:longitude,login_range',
            'longitude'                             => 'nullable|numeric|min:-180|max:180|required_with:latitude,login_range',
            'telephone_1'                           => 'digits_between:0,4|required_with:telephone_2,telephone_3',
            'telephone_2'                           => 'digits_between:0,4|required_with:telephone_1,telephone_3',
            'telephone_3'                           => 'digits_between:0,5|required_with:telephone_1,telephone_2',
            'chief_first_name'                      => 'max:80',
            'chief_last_name'                       => 'max:80',
            'chief_first_name_furigana'             => 'nullable|max:240|regex:/^([ァ-ヾ｡-ﾟ1-9１-９、。゛゜　 ])*$/u',
            'chief_last_name_furigana'              => 'nullable|max:240|regex:/^([ァ-ヾ｡-ﾟ1-9１-９、。゛゜　 ])*$/u',
            'chief_email'                           => 'nullable|email',
            'enable'                                => 'boolean',
            'work_location_id'                      =>  Rule::in($work_location_list),
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

        // When this is an update request, we won't check the uniqueness of presentation_id, if it does not change
        $work_address = $this->route('work_address');
        if (is_object($work_address)) {
            if ($this->input('presentation_id') != $work_address->presentation_id) {
                $validator->addRules(['presentation_id' => 'unique:work_addresses']);
            }
        } else {
            $validator->addRules(['presentation_id' => 'unique:work_addresses']);
        }

        // Post validation processes
        $validator->after(function ($validator) {
            PostValidationProcesses::telephone($this);
            PostValidationProcesses::postalCode($this);
        });
    }
}
