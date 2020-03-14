<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use App\AdminAccount;

class APIJupiterLoginRequest extends FormRequest
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
            'manager_presentation_id'   => 'required|max:60',
            'manager_password'          => 'required|max:255',
            'tablet_id'                 => 'required|max:255',
            'company_code'              => 'required|max:32',
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
        $validator->after(function ($validator) {
            $username = $this->manager_presentation_id;
            $list_account = AdminAccount::where('username', $username)->get();
            foreach ($list_account as $account) {
                if ($account->password === $this->manager_password) return;
            }
            $validator->errors()->add('Error account', 'Username or password in valid!');
        });
    }
}
