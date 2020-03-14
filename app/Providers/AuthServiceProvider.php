<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Register all the gates

        // Company Information Page's gates
        Gate::define('change_company_info', 'App\Policies\AuthorityPolicy@changeCompanyInformation');
        Gate::define('view_company_info', 'App\Policies\AuthorityPolicy@viewCompanyInformation');

        // Manager pages's gate
        Gate::define('change_manager_info', 'App\Policies\AuthorityPolicy@changeManagerInformation');

        // Work Location Pages's gates
        Gate::define('change_work_location_info', 'App\Policies\AuthorityPolicy@changeWorkLocationInformation');
        Gate::define('view_work_location_info', 'App\Policies\AuthorityPolicy@viewWorkLocationInformation');

        // Work Address Pages's gates
        Gate::define('change_work_address_info', 'App\Policies\AuthorityPolicy@changeWorkAddressInformation');
        Gate::define('view_work_address_info', 'App\Policies\AuthorityPolicy@viewWorkAddressInformation');
        Gate::define('change_work_address_s_work_location', 'App\Policies\AuthorityPolicy@changeWorkAddressWorkLocationInformation');

        // Employee Pages's gates
        Gate::define('change_employee_basic_info', 'App\Policies\AuthorityPolicy@changeEmployeeBasicInformation');
        Gate::define('view_employee_basic_info', 'App\Policies\AuthorityPolicy@viewEmployeeBasicInformation');
        Gate::define('see_employee_tab', 'App\Policies\AuthorityPolicy@seeEmployeeTabInNavigationBar');
        Gate::define('change_employee_s_work_location', 'App\Policies\AuthorityPolicy@changeEmployeeWorkLocationInformation');
        Gate::define('change_employee_work_info', 'App\Policies\AuthorityPolicy@changeEmployeeWorkInformation');
        Gate::define('view_employee_work_info', 'App\Policies\AuthorityPolicy@viewEmployeeWorkInformation');

        // Calendar Pages's gates
        Gate::define('view_calendar', 'App\Policies\AuthorityPolicy@viewCalendarInformation');
        Gate::define('change_calendar', 'App\Policies\AuthorityPolicy@changeCalendarInformation');

        // Setting Pages's gates
        Gate::define('change_setting_info', 'App\Policies\AuthorityPolicy@changeSettingInformation');
        Gate::define('view_setting_info', 'App\Policies\AuthorityPolicy@viewSettingInformation');

        // Option Pages's gates
        Gate::define('change_option_info', 'App\Policies\AuthorityPolicy@changeStatusesSettingInformation');
        Gate::define('view_option_info', 'App\Policies\AuthorityPolicy@viewStatusesSettingInformation');
        Gate::define('change_department_info', 'App\Policies\AuthorityPolicy@changeDepartmentTypeSettingInformation');
        Gate::define('view_department_info', 'App\Policies\AuthorityPolicy@viewDepartmentTypeSettingInformation');
        Gate::define('view_option_item_info', 'App\Policies\AuthorityPolicy@viewOptionItemInformation');

        // Attendance Employee's pages's gates
        Gate::define('view_attendance_employee_working_day', 'App\Policies\AuthorityPolicy@viewAttendanceEmployeeWorkingDay');
        Gate::define('change_attendance_data', 'App\Policies\AuthorityPolicy@changeAttendanceData');
    }
}
