<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\WorkLocation;
use App\Employee;
use App\PlannedSchedule;
use App\WorkingTimestamp;
use App\EmployeeWorkingInformation;
use App\Observers\AddViewOrderNumberObserver;
use App\Observers\EmployeeObserver;
use App\Observers\WorkingTimestampObserver;
use App\Observers\PlannedScheduleObserver;
use App\Observers\EmployeeWorkingInformationObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register the model to the AddViewOrderNumberObserver to use the order function
        WorkLocation::observe(AddViewOrderNumberObserver::class);
        Employee::observe(AddViewOrderNumberObserver::class);
        Employee::observe(EmployeeObserver::class);

        // Working Information related events
        PlannedSchedule::observe(PlannedScheduleObserver::class);

        // Checklist Errors checking
        EmployeeWorkingInformation::observe(EmployeeWorkingInformationObserver::class);

        // Custom Validation Rules
        Validator::extend('year', '\App\Http\Requests\Reusables\ExtraValidations@year');
        Validator::extend('rest_day', '\App\Http\Requests\Reusables\ExtraValidations@restDay');
        Validator::extend('work_time', '\App\Http\Requests\Reusables\ExtraValidations@workTime');
        Validator::extend('time', '\App\Http\Requests\Reusables\ExtraValidations@time');
        Validator::extend('working_hour', '\App\Http\Requests\Reusables\ExtraValidations@workingHour');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
