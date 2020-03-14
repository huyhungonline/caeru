<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Setting;
use Illuminate\Http\Request;
use App\WorkLocation;
use App\Http\Requests\SettingRequest;
use Caeru;

class SettingController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('choose:singular');
        $this->middleware('require_company_complete:setting')->only(['edit', 'update']);
        $this->middleware('can:view_setting_info');
        $this->middleware('can:change_setting_info')->only(['update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $current_work_location =  session('current_work_location');

        if (!is_array($current_work_location)){

            $current_setting = null;

            if ($current_work_location === 'all') {

                $current_setting = $request->user()->company->setting;

            } else {

                $work_location = WorkLocation::find($current_work_location);

                $current_setting = $work_location->currentSetting();

            }

            return view('setting.edit', [
                'setting' => $current_setting,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\SettingRequest;  $request
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(SettingRequest $request)
    {
        // check this is company or work location
        $location =  $request->session()->get('current_work_location');
        $data = $request->only([
                'timezone',
                'salary_accounting_day',
                'pay_month',
                'pay_day',
                'start_day_of_week',
                'law_rest_day_mode',
                'start_time_round_up',
                'end_time_round_down',
                'break_time_round_up',
                'start_time_diff_limit',
                'end_time_diff_limit',
                'go_out_button_usage',
                'display_go_out_time',
                'use_overtime_button',
                'paid_holiday_after_joined_period',
                'paid_holiday_first_time_normal_type',
                'paid_holiday_first_time_4wdpw_type',
                'paid_holiday_first_time_3wdpw_type',
                'paid_holiday_first_time_2wdpw_type',
                'paid_holiday_first_time_1wdpw_type',
                'paid_holiday_increase_rate_normal_type',
                'paid_holiday_increase_rate_4wdpw_type',
                'paid_holiday_increase_rate_3wdpw_type',
                'paid_holiday_increase_rate_2wdpw_type',
                'paid_holiday_increase_rate_1wdpw_type'
            ]);

        $this->saveToCompanyOrWorkLocation($request, $data, $location);

        return Caeru::redirect('edit_setting');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
    }

    /**
     * Save the setting to the default setting (company's setting) or to this current work location setting.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  mix                          $data       data
     * @param  mix                          $location   either 'all' or a work location id
     * @return void
    */
    private function saveToCompanyOrWorkLocation($request, $data, $location)
    {
        if ($location == "all") {

            $company = $request->user()->company;

            $company->setting->update($data);

            if (!$company->initial_setting_completed) $company->update(['initial_setting_completed' => true]);

        } else {

            $work_location = WorkLocation::find($location);

            $work_location->saveCurrentSetting($data);

        }

        $request->session()->flash('success', '保存しました');
    }
}
