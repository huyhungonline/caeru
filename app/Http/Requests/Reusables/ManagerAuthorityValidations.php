<?php

namespace App\Http\Requests\Reusables;

use Illuminate\Validation\Rule;
use App\ManagerAuthority;

class ManagerAuthorityValidations
{
    protected static $three_option = [
        ManagerAuthority::CHANGE,
        ManagerAuthority::BROWSE,
        ManagerAuthority::NOTHING
    ];

    protected static $two_option = [
        ManagerAuthority::BROWSE,
        ManagerAuthority::NOTHING
    ];

    public static function addRules($validator)
    {
        $validator->addRules([
            'company_information'               => Rule::in(ManagerAuthorityValidations::$three_option),
            'work_location_information'         => Rule::in(ManagerAuthorityValidations::$three_option),
            'work_address_information'          => Rule::in(ManagerAuthorityValidations::$three_option),
            'employee_basic_information'        => Rule::in(ManagerAuthorityValidations::$three_option),
            'employee_work_information'         => Rule::in(ManagerAuthorityValidations::$three_option),
            'calendar_setting'                  => Rule::in(ManagerAuthorityValidations::$three_option),
            'setting'                           => Rule::in(ManagerAuthorityValidations::$three_option),
            'statuses_setting'                  => Rule::in(ManagerAuthorityValidations::$three_option),
            'department_type_setting'           => Rule::in(ManagerAuthorityValidations::$three_option),
            'work_data_management'              => Rule::in(ManagerAuthorityValidations::$two_option),
            'work_data_search'                  => Rule::in(ManagerAuthorityValidations::$two_option),
            'work_data_calculation'             => Rule::in(ManagerAuthorityValidations::$two_option),
            'work_data_detail'                  => Rule::in(ManagerAuthorityValidations::$two_option),
            'work_data_personal_detail'         => Rule::in(ManagerAuthorityValidations::$two_option),
            'work_data_modify'                  => 'boolean',
            'work_data_modify_request_confirm'  => 'boolean',
            'work_data_paid_holiday_management' => Rule::in(ManagerAuthorityValidations::$three_option),
            'work_data_paid_holiday_detail'     => Rule::in(ManagerAuthorityValidations::$three_option),
            'work_data_addresses'               => Rule::in(ManagerAuthorityValidations::$two_option),
            'work_data_address_detail'          => Rule::in(ManagerAuthorityValidations::$three_option),
            'work_data_address_work_detail'     => Rule::in(ManagerAuthorityValidations::$three_option),
            'approval_level_one'                => 'boolean',
            'approval_level_two'                => 'boolean',
        ]);
    }
}