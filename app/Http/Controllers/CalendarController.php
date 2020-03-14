<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\WorkLocation;
use App\CalendarRestDay;
use App\CalendarTotalWorkTime;
use App\Http\Requests\CalendarRequest;

class CalendarController extends Controller
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
        $this->middleware('require_company_complete:calendar')->only('edit', 'update');
        $this->middleware('can:view_calendar');
        $this->middleware('can:change_calendar')->only('update');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $this_year = date('Y');

        $result = $this->getDataByYear($this_year);

        Javascript::put([
            'rest_days'             => $result['calendar_rest_days'],
            'work_times'            => $result['total_work_times'],
            'accounting_day'        => $result['salary_accounting_day'],
            'work_location_id'      => $result['work_location_id'],
        ]);

        return view('calendar.edit');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CalendarRequest   $request
     * @return \Illuminate\Http\Response
     */
    public function update(CalendarRequest $request)
    {
        $changed_days = $request->input('changed_rest_days');
        $changed_times = $request->input('changed_work_times');

        // This variable is used when this is the first time this company's calendar was saved.
        $refresh_page = false;

        $work_location = $request->input('id');
        if ($work_location) {
            $rest_days_criteria = [ 'work_location_id' => $work_location ];
            $work_time_criteria = [ 'work_location_id' => $work_location ];
        } else {
            $company = $request->user()->company;
            $rest_days_criteria = [ 'company_id' => $company->id ];
            $work_time_criteria = [ 'company_id' => $company->id ];

            // If this is the first time this company's calendar was saved, then this page need to be refreshed
            if (!$company->initial_calendar_completed) {
                $company->update(['initial_calendar_completed' => true]);
                $request->session()->flash('success', '保存しました');
                $refresh_page = true;
            }
        }


        foreach ($changed_days as $data) {
            $rest_days_criteria['assigned_date'] = $data['day'];
            CalendarRestDay::updateOrCreate(
                $rest_days_criteria,
                [ 'type' => $data['type'] ]
            );
        }
        foreach ($changed_times as $data) {
            $work_time_criteria['year'] = explode('-', $data['month'])[0];
            $work_time_criteria['month'] = explode('-', $data['month'])[1];
            CalendarTotalWorkTime::updateOrCreate(
                $work_time_criteria,
                [ 'time' => $data['time'] ]
            );
        }

        return [
            'success'   => '保存しました',
            'refresh'   => ($refresh_page == true) ? true : false,
        ];
    }


    /**
     * Load the calendar's data
     *
     * @param int   $year
     * @return array
     */
    public function load($year)
    {
        $data = $this->getDataByYear($year);

        return [
            'calendar_rest_days'    => $data['calendar_rest_days'],
            'total_work_times'      => $data['total_work_times'],
        ];
    }


    /**
     * Get the data for the calendar base on year
     *
     * @param int       $year
     * @return array
     */
    private function getDataByYear($year)
    {
        $chosen_work_location = session('current_work_location');

        $calendar_rest_days = [];
        $total_work_times = [];
        $salary_accounting_day = null;

        // Get data of the company, or 'default' data
        $default_calendar_rest_days = Auth::user()->company->calendarRestDays()->whereYear('assigned_date', $year)->get(['type', 'assigned_date'])->keyBy(function($item) {
            return date('Y-n-j', strtotime($item->assigned_date));
        })->toArray();
        $default_total_work_times = Auth::user()->company->calendarTotalWorkTimes()->where('year', '=', $year)->get(['time', 'year', 'month'])->keyBy(function($item) {
            return $item->year . '-' . $item->month;
        })->toArray();
        $default_salary_accounting_day = Auth::user()->company->setting->salary_accounting_day;


        if ($chosen_work_location != "all") {

            $work_location = WorkLocation::find($chosen_work_location);

            // Get data of this work location
            $work_location_calendar_rest_days = $work_location->calendarRestDays()->whereYear('assigned_date', $year)->get(['type', 'assigned_date'])->keyBy(function($item) {
                return date('Y-n-j', strtotime($item->assigned_date));
            })->toArray();
            $work_locaiton_total_work_times = $work_location->calendarTotalWorkTimes()->where('year', '=', $year)->get(['time', 'year', 'month'])->keyBy(function($item) {
                return $item->year . '-' . $item->month;
            })->toArray();
            $work_location_salary_accounting_day = ($work_location->setting) ? $work_location->setting->salary_accounting_day : null;


            // Then, we merge them using the php's merge array function, in the case of duplicated keys, which means the work location have a difference
            // from the company's calendar setting, the company data will be overwrite by the work location data.
            $calendar_rest_days = array_merge($default_calendar_rest_days, $work_location_calendar_rest_days);
            $total_work_times = array_merge($default_total_work_times, $work_locaiton_total_work_times);
            $salary_accounting_day = ($work_location_salary_accounting_day) ? $work_location_salary_accounting_day : $default_salary_accounting_day;

        } else {
            $calendar_rest_days = $default_calendar_rest_days;
            $total_work_times = $default_total_work_times;
            $salary_accounting_day = $default_salary_accounting_day;
        }

        return [
            'calendar_rest_days'    => $calendar_rest_days,
            'total_work_times'      => $total_work_times,
            'salary_accounting_day' => $salary_accounting_day,
            'work_location_id'      => isset($work_location) ? $work_location->id : null,
        ];
    }
}
