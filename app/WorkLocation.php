<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Reusables\TodofukenTrait;
use App\Reusables\EnableTrait;
use App\Setting;
use DB;

class WorkLocation extends Model
{
    use SoftDeletes, TodofukenTrait, EnableTrait;


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the company of this work location
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the employees of this work location
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the checklists of this work location
     */
    public function checklistItems(){
        return $this->hasManyThrough(ChecklistItem::class, Employee::class);
    }
    /**
     * Get all the managers managing this work location
     */
    public function managers()
    {
        return $this->belongsToMany(Manager::class);
    }

    /**
     * Get the work addresses of this work location (if it has any)
     */
    public function workAddresses()
    {
        return $this->hasMany(WorkAddress::class);
    }

    /**
     * Get the schedules of this work location (if it has any)
     */
    public function schedules()
    {
        return $this->hasMany(PlannedSchedule::class);
    }

    /**
     * Get holidays
     */
    public function calendarRestDays()
    {
        return $this->hasMany(CalendarRestDay::class);
    }

    /**
     * Get default holidays
     */
    public function defaultCalendarRestDays()
    {
        return $this->company->calendarRestDays();
    }

    /**
     * Get total work time by months
     */
    public function calendarTotalWorkTimes()
    {
        return $this->hasMany(CalendarTotalWorkTime::class);
    }

    /**
     * Get default total work time by months
     */
    public function defaultCalendarTotalWorkTimes()
    {
        return $this->company->calendarTotalWorkTimes();
    }

    /**
     * Get setting
     */
    public function setting()
    {
        return $this->hasOne(Setting::class);
    }

    /**
     * Get default setting
     */
    public function defaultSetting()
    {
        return $this->company->setting;
    }

    /**
     * Get the current setting of this work location. If this work location does not have any setting, that means it uses the default setting (company's).
     * If it has its own setting, that setting need to be merged with the default setting.
     */
    public function currentSetting()
    {
        if (!isset($this->setting)) {

            return $this->defaultSetting();

        } else {

            $work_location_only_setting = $this->setting;

            // Replicate from the company's setting
            $current_setting = $this->defaultSetting()->replicate();

            // Then merge anything of this work location's setting that is different from the company's.
            foreach ($work_location_only_setting->attributesToArray() as $key => $value) {

                if (isset($value)) {
                    $current_setting->setAttribute($key, ($value !== config('caeru.empty')) ? $value : null);
                }

                $current_setting->company_id = null;

            }

            return $current_setting;
        }
    }

    /**
     * Save the current setting of this work location. If the setting have any difference with company's, save that difference into this work location's setting instance.
     */
    public function saveCurrentSetting($data)
    {

        $default_setting = $this->defaultSetting();

        foreach ($data as $key => $value) {
            if ($data[$key] == $default_setting->$key  && !isset($this->setting->$key)) {
                unset($data[$key]);
            }else{
                if ($data[$key] == "") $data[$key]= config('caeru.empty');
            }
        }

        if (count($data) !== 0)
            Setting::updateOrCreate( ['work_location_id' => $this->id], $data);

    }


    /**
     * Return list of activating departments of this work location
     */
    public function activatingWorkStatuses()
    {
        $unused_work_statuses = DB::table('work_statuses_unused')->where('work_location_id', $this->id)->pluck('work_status_id')->toArray();
        return WorkStatus::where('company_id', $this->company->id)->whereNotIn('id', $unused_work_statuses)->orderBy('id')->get();
    }

    /**
     * Return list of activating work statuses of this work location
     */
    public function activatingRestStatuses()
    {
        $unused_rest_statuses = DB::table('rest_statuses_unused')->where('work_location_id', $this->id)->pluck('rest_status_id')->toArray();
        return RestStatus::where('company_id', $this->company->id)->whereNotIn('id', $unused_rest_statuses)->orderBy('id')->get();
    }

    /**
     * Return list of activating rest statuses of this work location
     */
    public function activatingDepartments()
    {
        $unused_departments = DB::table('departments_unused')->where('work_location_id', $this->id)->pluck('department_id')->toArray();
        return Department::where('company_id', $this->company->id)->whereNotIn('id', $unused_departments)->orderBy('id')->get();
    }

    /**
     * Get an array of rest days of this work location (already merge with company's properly). This array only contains the date string.
     * Can accept 2 parameters to limit the result by date.
     *
     * @param string    $start_date
     * @param string    $end_date
     * @return array
     */
    public function getArrayRestDays($start_date = null, $end_date = null)
    {
        $rest_days = $this->getRestDays($start_date, $end_date);

        $rest_days = collect($rest_days)->filter(function($value) {
            return $value['type'] != 0;
        })->pluck('assigned_date');

        return $rest_days;
    }

    /**
     * Get a collection of rest days of this work location, like the above function.
     *
     * @param string    $start_date
     * @param string    $end_date
     * @return collection
     */
    public function getRestDays($start_date = null, $end_date = null)
    {
        $company_rest_days = $this->defaultCalendarRestDays();
        $work_location_rest_days = $this->calendarRestDays();

        if ($start_date) {
            $company_rest_days->where('assigned_date', '>=', $start_date);
            $work_location_rest_days->where('assigned_date', '>=', $start_date);
        }

        if ($end_date) {
             $company_rest_days->where('assigned_date', '<=', $end_date);
             $work_location_rest_days->where('assigned_date', '<=', $end_date);
        }

        $company_rest_days = $company_rest_days->orderBy('assigned_date')->get(['type', 'assigned_date'])->keyBy(function($item) {
            return date('Y-n-j', strtotime($item->assigned_date));
        })->toArray();
        $work_location_rest_days = $work_location_rest_days->orderBy('assigned_date')->get(['type', 'assigned_date'])->keyBy(function($item) {
            return date('Y-n-j', strtotime($item->assigned_date));
        })->toArray();
        $combine_rest_days = array_merge($company_rest_days, $work_location_rest_days);

        return $combine_rest_days;
    }

}
