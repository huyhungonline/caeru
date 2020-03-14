<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\ManagerAuthority;
use App\Setting;
use App\WorkLocation;
use App\Company;
use App\Employee;
use Caeru;
use Constants;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(
            [
                'attendance.work_address.attendance_shift',
                'attendance.work_address.attendance_work_place',
                'attendance.work_address.attendance_work_info'

            ],
            'App\Http\ViewComposers\EmployeeApprovalPageComposer'
        );
        // This composer transform telephone, postal_code, fax fields to be display in forms
        View::composer(
            [
                'company.edit',
                'work_location.edit',
                'manager.edit',
                'employee.edit',
                'work_address.edit',
            ],
            'App\Http\ViewComposers\SeparatedFieldsComposer'
        );


        // This composer transform the list of ip addresses to be displayed in forms
        View::composer(
            [
                'manager.edit'
            ],
            'App\Http\ViewComposers\IpAddressComposer'
        );


        // Any page that have the work location picker
        View::composer(
            [
                'employee.list',
                'employee.form',
                'employee.edit_work',
                'work_address.list',
                'work_address.form',
                'work_address.edit_detail',
                'calendar.edit',
                'setting.edit',
                'option.edit_work_rest',
                'option.edit_department',
                'checklist.list',
                'totalization.list',
                'paidholidayinformation.list',
                'paidholidayinformation.edit',
            ],
            'App\Http\ViewComposers\WorkLocationPickerComposer'
        );


        // Add todofuken list to the views
        View::composer(
            [
                'company.edit',
                'work_location.form',
                'employee.form',
                'work_address.form',
            ],
            function ($view) {
                $view->with([
                    'todofuken_list' => \DB::table('todofukens')->pluck('name', 'id')
                ]);
            }
        );


        // Add work locations list to the views
        View::composer(
            [
                'manager.new',
                'manager.edit'
            ],
            function ($view) {
                $view->with([
                    'work_locations' => Auth::user()->company->workLocations()->orderBy('view_order')->pluck('name', 'id')
                ]);
            }
        );


        // Add the work location list to the employee and work address forms. This list is based on the current work location.
        View::composer(
            [
                'work_address.form',
                'employee.form'
            ],
            function ($view) {
                $list = null;
                $current_work_location = session('current_work_location');
                $company = Auth::user()->company;

                if ($current_work_location === 'all')
                    $work_locations = $company->workLocations()->enable()->orderBy('view_order')->pluck('name', 'id');
                elseif (is_array($current_work_location)) {
                    $work_locations = $company->workLocations()->enable()->whereIn('work_locations.id', $current_work_location)->orderBy('view_order')->pluck('name', 'id');
                } else {
                    $work_locations = $company->workLocations()->where('work_locations.id', $current_work_location)->orderBy('view_order')->pluck('name', 'id');
                }

                $view->with([
                    'work_locations' => $work_locations,
                ]);
            }
        );


        // Prepare the department list and chiefs information for employee's form.
        View::composer(
            [
                'employee.form'
            ],
            'App\Http\ViewComposers\EmployeeFormComposer'
        );


        // Add the list of all departments to the view of the employee search box form
        View::composer(
            [
                'employee.search_box'
            ],
            'App\Http\ViewComposers\EmployeeSearchBoxComposer'
        );

        // These pages also use employee seach box, but they differ a little bit
        View::composer(
            [
                'employee.form',
                'employee.edit_work'
            ],
            function ($view) {
                Javascript::put([
                    'target'        => Caeru::route('employees_list'),
                ]);
            }
        );


        // Provide the list of work address's name, address and employee's name for the work address search form
        View::composer(
            [
                'work_address.search_box'
            ],
            'App\Http\ViewComposers\WorkAddressSearchBoxComposer'
        );

        // These pages also use work address seach box, but they differ a little bit
        View::composer(
            [
                'work_address.form',
                'work_address.edit_detail'
            ],
            function ($view) {
                Javascript::put([
                    'target'        => Caeru::route('work_address_list'),
                    'default_hide'  => true,
                ]);
            }
        );


        // Add the authority options to the manager's form
        View::composer(
            [
                'manager.new',
                'manager.edit'
            ],
            function ($view) {
                $view->with([
                    'three_authority_types' => [
                        ManagerAuthority::CHANGE        =>      '追加・変更可能',
                        ManagerAuthority::BROWSE        =>      '閲覧',
                        ManagerAuthority::NOTHING       =>      '非表示',
                    ],
                    'two_authority_types' => [
                        ManagerAuthority::BROWSE        =>      '閲覧',
                        ManagerAuthority::NOTHING       =>      '非表示',
                    ],
                ]);
            }
        );


        // Add various select types array in the configuration to various views
        View::composer(
            'company.edit', function ($view) {
                $view->with([
                    'date_separate_types' => [
                        Company::APPLY_TO_THE_DAY_BEFORE => 'までを前日に入れる',
                        Company::APPLY_TO_THE_DAY_AFTER => 'から24:00までを翌日に入れる',
                    ],
                ]);
            }
        );

        View::composer(
            [
                'employee.new',
                'employee.edit',
                'employee.search_box',
                'paidholidayinformation.edit',
            ],
            function ($view) {

                $view->with([
                    'genders'           => Constants::genders(),
                    'schedule_types'    => Constants::scheduleTypes(),
                    'employment_types'  => Constants::employmentTypes(),
                    'salary_types'      => Constants::salaryTypes(),
                    'work_statuses'     => Constants::workStatuses(), 
                    'holiday_bonus_types' =>Constants::holidayBonusTypes(),
                ]);
            }
        );



        // Add various presentational variables for the page employee work edit page
        View::composer(
            [
                'employee.edit_work',
            ], 
            function ($view) {

                // Prepare the presentation data for the Vue work-schedule component
                $work_locations = Auth::user()->company->workLocations()->enable()->orderBy('view_order')->get(['id', 'name'])->toArray();
                $presentation_data = [
                    "work_locations"    => $work_locations,
                    // "frequency_types"   => config('caeru.frequency_types'),
                    "salary_types"      => Constants::salaryTypes(),
                    "editable"          => Auth::user()->can('change_employee_work_info'),
                ];

                // If use the address system then add the information about the work addresses
                if (true == $use_address_system = Auth::user()->company->use_address_system) {
                    $work_addresses = Auth::user()->company->workAddresses()->enable()->workLocationEnable()->get()->transform(function($item, $key) {
                        return [
                            'id'                =>  $item->id,
                            'name'              =>  $item->name,
                            'work_location_id'  =>  $item->workLocation->id,
                            // 'description'       =>  $item->workLocation->name,

                        ];
                    })->toArray();

                    $presentation_data["autocomplete_data"] = $work_addresses;
                    $presentation_data["display_address"] = $use_address_system;
                    // $presentation_data["display_employee"] = true;
                }

                Javascript::put([
                    "presentation_data" => $presentation_data,
                ]);

                $view->with([
                    'holiday_bonus_types' => array_slice(Constants::holidayBonusTypes(), 1, null, true),
                    'normal_type_value' => config('constants.normal_bonus'),
                ]);
            }
        );


        // Add various presentational variables for the page work address edit detail page
        View::composer(
            [
                'work_address.edit_detail'
            ], 
            function ($view) {
                $presentation_data = [
                    // "frequency_types"   => config('caeru.frequency_types'),
                    "salary_types"      => Constants::salaryTypes(),
                    "editable"          => Auth::user()->can('change_work_address_info'),
                ];

                $presentation_data["display_employee"] = true;

                if ( Auth::user()->company_wide_authority)
                    $arr_work_location_id = Auth::user()->company->workLocations->pluck('id')->toArray();
                else
                    $arr_work_location_id = Auth::user()->workLocations->pluck('id')->toArray();

                $list_employee = Employee::whereIn('work_location_id', $arr_work_location_id)->workLocationEnable()->working()->get()->transform(function($item, $key) {
                        return [
                            'id'                =>  $item->id,
                            'name'              =>  $item->last_name.$item->first_name
                        ];
                    })->toArray();

                $presentation_data["autocomplete_data"] = $list_employee;

                Javascript::put([
                    "presentation_data" => $presentation_data,
                ]);
            }
        );


        // Add list time zones to the setting's views
        View::composer(
            'setting.edit', function ($view) {

                // Get timezones list
                $timezones = \DB::table('timezones')->get()->keyBy('id')->map(function($timezone) {
                    return $timezone->name_id . ' ' . $timezone->utc_offset_string;
                })->toArray();

                $view->with([
                    'data_time_zones' => $timezones,
                    'data_pay_month' => [
                        Setting::THIS_MONTH             =>      '当月',
                        Setting::NEXT_MONTH             =>      '翌月',
                        Setting::NEXT_NEXT_MONTH        =>      '翌々月',
                    ],
                    'data_day_of_week'          =>      Constants::dayOfTheWeek(),
                    'use_go_out'                =>      Setting::USE_GO_OUT_BUTTON,
                    'use_as_break_time'         =>      Setting::USE_AS_BREAK_TIME_BUTTON,
                    'not_use_go_out'            =>      Setting::NOT_USE_GO_OUT_BUTTON,
                    'law_rest_day_modes'        =>      config('caeru.law_rest_day_modes'),
                ]);
            }
        );


        // Mask the true view order, and generate a fake view order so that they can be continous
        View::composer(
            'employee.list_table',
            'App\Http\ViewComposers\MaskedViewOrderComposer'
        );


        // Processing the logic for the pages that have search result navigation band
        View::composer(
            'layouts.search_result_navigation',
            'App\Http\ViewComposers\SearchResultNavigationComposer'
        );

        View::composer(
            'paidholidayinformation.edit',
            'App\Http\ViewComposers\SearchResultNavigationComposer'
        );
        // Processing the presentation data for the employee approval page
        View::composer(
            'employee.approval',
            'App\Http\ViewComposers\EmployeeApprovalPageComposer'
        );
    }
}
