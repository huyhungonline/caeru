<?php

namespace App\Observers;

use NationalHolidays;
use DB;
use Carbon\Carbon;
use App\PlannedSchedule;
use App\WorkLocation;
use App\Employee;
use App\EmployeeWorkingDay;
use App\EmployeeWorkingInformation;
use App\WorkAddressWorkingDay;
use App\WorkAddressWorkingInformation;
use App\WorkAddressWorkingEmployee;

class PlannedScheduleObserver
{

    /**
     * When a planned schedule is being created, if the employee or work address, associating with this planned schedule, does not have any
     * planned schedule yet, initialize the working days for that employee or work address
     *
     * @param PlannedSchedule $schedule
     * @return void
     */
    public function creating(PlannedSchedule $schedule)
    {

        $period = $this->getDatePeriod($schedule);

        $employee = $schedule->employee;

        $this->initializeWorkingDay($employee, $period);

        $work_address = $schedule->workAddress;

        if ($work_address)
            $this->initializeWorkingDay($work_address, $period);


    }

    /**
     * When a PlannedSchedule is created, create all the coresponding WorkingInformations (for Employee or for Work Address)
     *
     * @param PlannedSchedule $schedule
     * @return void
     */
    public function created(PlannedSchedule $schedule)
    {

        // If this schedule is related to a work address...
        if ($schedule->work_address_id) {

            $this->initializeWorkAddressWorkingInformationsMatchedTimeRange($schedule);

        } else {

            $this->initializeEmployeeWorkingInformationsMatchedTimeRange($schedule);

        }
    }

    /**
     * When a PlannedSchedule is being deleted, first delete all the relating working information(employee's or work address's) and/or work address's working employee instances
     *
     * @param PlannedSchedule $schedule
     * @return void
     */
    public function deleting(PlannedSchedule $schedule)
    {
        $this->deleteRelatingModels($schedule);
    }

    /**
     * When a PlannedSchedule is being updated, if its time range is changed, then delete all the related Models and initialize new ones
     *
     * @param PlannedSchedule $schedule
     * @return void
     */
    public function updating(PlannedSchedule $schedule)
    {
        if (($this->standardizedDate($schedule->start_date) != $schedule->getOriginal('start_date')) ||
            ($this->standardizedDate($schedule->end_date) != $schedule->getOriginal('end_date')) ||
            ($this->standardizedDaysOfWeek($schedule->working_days_of_week) != $schedule->getOriginal('working_days_of_week')) ||
            ($schedule->rest_on_holiday != $schedule->getOriginal('rest_on_holiday')) ||
            ($schedule->prioritize_company_calendar != $schedule->getOriginal('prioritize_company_calendar')))
        {
            $this->deleteRelatingModels($schedule);

            if ($schedule->work_address_id) {
                $this->initializeWorkAddressWorkingInformationsMatchedTimeRange($schedule);
            } else {
                $this->initializeEmployeeWorkingInformationsMatchedTimeRange($schedule);
            }
        }
    }

    /**
     * Create WorkingDay instances of Employee or WorkAddress base on a period of time.
     *
     * @param Eloquent $model               it's either employee or work address
     * @param \DatePeriod $period           the period to insert working day records
     * @return void
     */
    protected function initializeWorkingDay($model, $period)
    {

        // For perfomance, we will be using QueryBuilder instead of Eloquent in this part
        $to_be_created_working_days = [];

        // For each day, create a set of data, then add into a container array...
        foreach ($period as $day) {

            $working_day = '(' . $model->id . ', "' . $day->format('Y-m-d') . '", "' . Carbon::now() . '")';

            $to_be_created_working_days[] = $working_day;
        }

        $config = DB::getConfig();

        // ..then use that array to generate the sql query
        if (is_a($model, Employee::class)) {

            DB::insert('insert ignore into ' . $config['database'] . '.' . $config['prefix'] . 'employee_working_days (employee_id, date, created_at) values ' . implode(',', $to_be_created_working_days));

        } else {

            DB::insert('insert ignore into ' . $config['database'] . '.' . $config['prefix'] . 'work_address_working_days (work_address_id, date, created_at) values ' . implode(',', $to_be_created_working_days));
        }
    }

    /**
     * Generate the \DatePeriod base on the start_date and end_date of the given schedule.
     *
     * @param PlannedSchedule $schedule     the given schedule
     * @return \DatePeriod $period          the DatePeriod instance
     */
    protected function getDatePeriod($schedule)
    {
        $start_date = null;
        $end_date = null;

        if ($schedule->start_date) {

            $start_date = new \DateTime($schedule->start_date);

            if ($schedule->end_date) {
                $end_date = new \DateTime($schedule->end_date);
            } else {
                $end_date = new \DateTime($schedule->start_date);
                $end_date = new \DateTime($end_date->modify('+4 months')->format('Y-m' . '-01'));
            }

        // Have end_date but start_date is empty
        } elseif ($schedule->end_date) {

            $start_date = new \DateTime($schedule->end_date);
            $start_date = new \DateTime($start_date->modify('-4 months')->format('Y-m-t'));

            $end_date = new \DateTime($schedule->end_date);

        // Both start_date and end_date is empty
        } else {

            $start_date = new \DateTime();

            $end_date = new \DateTime('+4 months');
            $end_date = new \DateTime($end_date->format('Y-m' . '-01'));

        }

        $interval = \DateInterval::createFromDateString('1 day');

        return new \DatePeriod($start_date, $interval, $end_date);
    }

    /**
     * Create all the EmployeeWorkingInformation for EmployeeWorkingDays, that match with the time range of the given schedule and are not concluded yet
     *
     * @param PlannedSchedule   $schedule
     * @return void
     */
    protected function initializeEmployeeWorkingInformationsMatchedTimeRange(PlannedSchedule $schedule)
    {

        // Find all the EmployeeWorkingDay that is not concluded yet and match with the time range of the schedule
        $working_days = EmployeeWorkingDay::where('employee_id', $schedule->employee_id)
                        ->notConcluded()
                        ->notHaveManuallyModifiedEmployeeWorkingInformationThatAssociateWithThisSchedule($schedule->id);
        $working_days = $this->findWorkingDaysMatchedTimeRange($working_days, $schedule);


        // And create one EmployeeWorkingInformation for each of those EmployeeWorkingDay
        // Also, For perfomance we won't use Eloquent here, instead, we'll use the QueryBuilder
        $to_be_created_working_info = [];

        foreach ($working_days as $day) {

            $to_be_created_working_info[] = [
                'employee_working_day_id'   => $day->id,
                'planned_schedule_id'       => $schedule->id,
                'created_at'                => Carbon::now(),
            ];
        }

        DB::table('employee_working_informations')->insert($to_be_created_working_info);

    }

    /**
     * Create all the WorkAddressWorkingInformation for WorkAddressWorkingDays, that match with the time range of the given schedule.
     * At the same time, also create suitable WorkAddressWorkingEmployee and EmployeeWorkingInformation.
     *
     * @param PlannedSchedule   $schedule
     * @return void
     */
    protected function initializeWorkAddressWorkingInformationsMatchedTimeRange(PlannedSchedule $schedule)
    {
        $employee_working_days = EmployeeWorkingDay::where('employee_id', $schedule->employee_id)
                                    ->notConcluded()
                                    ->notHaveManuallyModifiedEmployeeWorkingInformationThatAssociateWithThisScheduleThroughWorkAddressWorkingEmployee($schedule->id)
                                    ->orderBy('date')->get(['id', 'date']);

        // Find all WorkAddressWorkingDay that match with the time range of the schedule
        $working_days = WorkAddressWorkingDay::where('work_address_id', $schedule->work_address_id)->whereIn('date', $employee_working_days->pluck('date'))->orderBy('date');
        $working_days = $this->findWorkingDaysMatchedTimeRange($working_days, $schedule);

        $neccessary_infos = [];

        // TODO: right now, the code of Part a, b and c is very un-efficenct (use to much queries generate much more overhead instead of a bulk query),
        // so we'll have to come back someday and resolve this problem
        foreach ($working_days as $day) {

            // ** Part a: retrieve working information of a working day
            $working_informations = $day->workAddressWorkingInformations;

            $match = $working_informations->first(function($info) use($schedule) {
                return ($info->planned_start_work_time == $schedule->start_work_time) &&
                        ($info->planned_end_work_time == $schedule->end_work_time);
            });

            $employee_working_information_id = null;

            // ** Part b: generate Employee's working information
            if (!$schedule->candidating_type == true) {

                $employee_working_day_id = $employee_working_days->first(function($employee_day) use($day) {
                    return $employee_day['date'] == $day->date;
                });

                $employee_working_information = new EmployeeWorkingInformation();
                $employee_working_information->employeeWorkingDay()->associate($employee_working_day_id);
                $employee_working_information->save();
                $employee_working_information_id = $employee_working_information->id;
            }

            if ($match) {

                $neccessary_info = [
                    'date'  => $day->date,
                    'work_address_working_information_id'   => $match->id,
                ];

                if ($employee_working_information_id) {
                    $neccessary_info['employee_working_information_id'] = $employee_working_information_id;
                }

                $neccessary_infos[] = $neccessary_info;

            } else {

                // ** Part c: generate Working Address's new working information
                $result = new WorkAddressWorkingInformation();
                $result->workAddressWorkingDay()->associate($day);
                $result->save();

                $neccessary_info = [
                    'date'  => $day->date,
                    'work_address_working_information_id'    => $result->id,
                ];

                if ($employee_working_information_id) {
                    $neccessary_info['employee_working_information_id'] = $employee_working_information_id;
                }

                $neccessary_infos[] = $neccessary_info;

            }

        }

        $to_be_created_working_employee = [];

        foreach ($neccessary_infos as $neccessary_info) {

            $to_be_created_working_employee[] = [
                'planned_schedule_id' => $schedule->id,
                'employee_id'   => $schedule->employee_id,
                'work_address_working_information_id' => $neccessary_info['work_address_working_information_id'],
                'employee_working_information_id' => isset($neccessary_info['employee_working_information_id']) ? $neccessary_info['employee_working_information_id'] : null,
                'created_at'    => Carbon::now(),
            ];

        }

        // ** Part d: generate Work Address's WorkingEmployee instances
        DB::table('work_address_working_employees')->insert($to_be_created_working_employee);

    }

    /**
     * Add query conditions base on the time range of the schedule. FYI, the "time range" of a schedule base on: start_date, end_date,
     * working_days_of_week and whether or not the employee work on a national holiday (rest_on_holiday check box).
     *
     * @param QueryBuilder      $query      a query builder instance
     * @param PlannedSchedule   $schedule   the given schedule
     * @return QueryBuilder     $query      that query builder instance with additional suitable where clauses.
     */
    protected function findWorkingDaysMatchedTimeRange($query, PlannedSchedule $schedule)
    {

        // Must be greater than or equal to the start date of the schedule
        if ($schedule->start_date) {
            $query->where('date', '>=', $schedule->start_date);
        }

        // Must be smaller than or equal to the end date of the schedule
        if ($schedule->end_date) {
            $query->where('date', '<=', $schedule->end_date);
        }

        // Satisfy the day of week condition
        $query->whereIn(\DB::raw('WEEKDAY(date)'), $schedule->chosenDaysOfWeek());

        // If the schedule does not include national holidays (in other words, the box '祝日は休む' is checked)
        if ($schedule->rest_on_holiday == true) {
            $query->whereNotIn('date', NationalHolidays::get($schedule->start_date, $schedule->end_date));
        }

        // If the schedule obey the WorkLocation calendar's rest day rule (企業カレンダーに従う)
        if ($schedule->prioritize_company_calendar == true) {

            $work_location = WorkLocation::find($schedule->work_location_id);
            $rest_days = $work_location->getArrayRestDays($schedule->start_date, $schedule->end_date);
            $query->whereNotIn('date', $rest_days);

        }

        return $query->get();
    }

    /**
     * Base on the schedule's relationship to delete all those related models
     * If the schedule has work_address_id then we have to delete EmployeeWorkingInformation, WorkAddressWorkingEmployee, and maybe
     * some WorkAddressWorkingInformation.
     * Else, we only have to delete EmployeeWorkingInformation.
     *
     * @param PlannedSchedule $schedule
     * @return void
     */
    protected function deleteRelatingModels(PlannedSchedule $schedule)
    {
        // If this schedule is related to work address then we have a lot to delete
        if ($schedule->work_address_id) {
            $working_employees = $schedule->workAddressWorkingEmployees()->notHavingConcludedWorkingInformations()
                                                                        ->notHavingManuallyModifiedWorkingInformations()
                                                                        ->notHavingContainingTimestampWorkingInformation()
                                                                        ->with('employeeWorkingInformation')
                                                                        ->with('workAddressWorkingInformation')
                                                                        ->get();

            $employee_working_info_ids = [];

            foreach ($working_employees as $working_employee) {
                $employee_working_info_ids[] = [
                    'id'    => $working_employee->employeeWorkingInformation->id,
                ];
            }

            DB::table('employee_working_informations')->whereIn('id', $employee_working_info_ids)->delete();
            DB::table('work_address_working_employees')->whereIn('id', $working_employees->pluck('id')->toArray())->delete();

            // For any WorkAddressWorkingInformation left empty by the two deletions above, delete it too
            $work_address_working_info_ids = WorkAddressWorkingInformation::has('workAddressWorkingEmployees', '=', 0)->pluck('id')->toArray();
            DB::table('work_address_working_informations')->whereIn('id', $work_address_working_info_ids)->delete();

        // If this schedule is not related to a work address, then we only need to remove the related EmployeeWorkingInformations
        } else {
            $working_info_ids = $schedule->employeeWorkingInformations()->notConcluded()->notManuallyModified()->notHaveTimestamp()->pluck('id')->toArray();

            DB::table('employee_working_informations')->whereIn('id', $working_info_ids)->delete();
        }
    }

    /**
     * Standardize the date string. Because the new date string maybe various in format ('Y-m-d', 'Y/m/d', 'Ymd', ...)
     *
     * @param string $date      the date string
     * @return string           convert the date string to format 'Y-m-d' (default format of database)
     */
    protected function standardizedDate($date)
    {
        $standardize_date = new \DateTime($date);
        return $standardize_date->format('Y-m-d');
    }

    /**
     * Standardize the the days_of_the_weeks data. So that we can compare.
     *
     * @param array $day_of_week        this is the default data type come right from the request. An array of booleans for each day of the week.
     * @return string                   implode that array, use a comma (',') as glue.
     */
    protected function standardizedDaysOfWeek($days_of_week)
    {
        return implode(',', $days_of_week);
    }
}