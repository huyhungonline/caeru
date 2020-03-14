<?php

use Illuminate\Database\Seeder;

class DefaultSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = \App\Company::first();
        $first_timezone = \DB::table('timezones')->first();

        DB::table('settings')->insert([
            
            'company_id' => $company->id,
            'timezone' => $first_timezone->id,
            'salary_accounting_day' => 0,
            'pay_month' => \App\Setting::NEXT_MONTH,
            'pay_day' => 0,
            'start_day_of_week' => last(array_keys(config('caeru.data_day_of_week'))),
            'law_rest_day_mode' => head(array_keys(config('caeru.law_rest_day_modes'))),
            'start_time_round_up' => 1,
            'end_time_round_down' => 1,
            'break_time_round_up' => 1,
            'start_time_diff_limit' => 1,
            'end_time_diff_limit' => 1,
            'go_out_button_usage' => \App\Setting::NOT_USE_GO_OUT_BUTTON,
            'display_go_out_time' => false,
            'use_overtime_button' => false,
            'paid_holiday_after_joined_period' => 6,
            'paid_holiday_first_time_normal_type' => 10,
            'paid_holiday_increase_rate_normal_type' => '11,12,13,14,15',
        ]);
    }
}
