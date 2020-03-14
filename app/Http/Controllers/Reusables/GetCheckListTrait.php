<?php
namespace App\Http\Controllers\Reusables;

use App\Setting;
use App\ChecklistItem;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

trait GetCheckListTrait
{

    /**
     * Gets the session.
     *
     * @param mixed $search_history_conditions
     */
    private function getSession() 
    {
        if(session()->has('checklist_search_history')) {
            return session()->get('checklist_search_history');
        }
        return null;
    }

    /**
     * Sets the session.
     *
     * @param  $search_history_conditions
     */
    private function setSession($search_history_conditions)
     {
        session(['checklist_search_history' => $search_history_conditions]);
    }

    /**
     * Gets the check list.
     *
     * @param array $search_history_conditions
     */
    private function getCheckList(array $search_history_conditions)
    {        
        $current_work_location = request()->session()->get('current_work_location');
        $checklists_timestamp_error  = ChecklistItem::checkList(
            ChecklistItem::TIMESTAMP_ERROR,
            $current_work_location,
            $search_history_conditions['beginDate'],
            $search_history_conditions['endDate'],
            $search_history_conditions['errlist'],
            $search_history_conditions['employeeId'],
            $search_history_conditions['employeeName']
        )->get();
        $checklists_confirm_needed  = ChecklistItem::checkList(
            ChecklistItem::CONFIRM_NEEDED, 
            $current_work_location,
            $search_history_conditions['beginDate'],
            $search_history_conditions['endDate'],
            $search_history_conditions['errlist'],
            $search_history_conditions['employeeId'],
            $search_history_conditions['employeeName']
        )->get();

        return [
            'checklists_confirm_needed'=>$checklists_confirm_needed,
            'checklists_timestamp_error'=>$checklists_timestamp_error,
        ];
    }

    /**
     * Gets the beginDate, endDate follow salary_accounting_day.
     *
     * @link http://carbon.nesbot.com/docs/
     * @throws \Exception if salary_accounting_day not exitst on settings table
     */
    private function getDate() {
        $current_work_location = request()->session()->get('current_work_location');
        $settingDate = (new Setting)->getSalaryDay($current_work_location);

        if(!$settingDate) throw new \Exception('Query invalid, cause settingDate not exists');
        //$currentDay = Carbon::now()->day;
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $year = request()->year?:$currentYear;
        $month = request()->month?:$currentMonth;

        if((int)$year == (int)$currentYear && (int)$month == (int)$currentMonth) {
            // If current date get time which salary date computed
            // Note: 
            //    - Time compare 00:00:00.000 after salary date, 
            //    example: settingDate 13, salaryTime will 2017-12-12 59:59:59.999 (follow currentMonth and currentYear), 
            //    - Date computed follow [http://carbon.nesbot.com/docs/](Carbon), 
            //    example: 2017-2-30 convert to 2017-3-2
            $salaryDate = Carbon::create($currentYear, $currentMonth, $settingDate, 0, 0, 0);
            $endDate = $salaryDate->copy();
            $beginDate = $salaryDate->copy();
            if(Carbon::now()->timestamp < $salaryDate->copy()->timestamp) {
                $endDate = $endDate;
                $beginDate->subMonth()->addDay();
                $dayOfWeekBegin = $beginDate->dayOfWeek;
                $dayOfWeekEnd = $endDate->dayOfWeek;
            } else {
                $endDate->addMonth();
                $beginDate->addDay();               
                $dayOfWeekBegin = $beginDate->dayOfWeek;
                $dayOfWeekEnd = $endDate->dayOfWeek;
            }
        } else {
            // If not current date, we get begin date of month to compute month 
            // which we use it to compute salary (void special date such as 2017-2-30, 2017-4-31)
            // Begin date computed from 00:00:00 after salary date same above
            $dateCompute = Carbon::createFromDate($year, $month, 1);
            $beginCompute = $dateCompute->copy()->subMonth();
            $endCompute = $dateCompute->copy();

            $yearBeginCompute = $beginCompute->year;
            $monthBeginCompute = $beginCompute->month;
            $dayBeginCompute = $settingDate;

            $beginDate = Carbon::create($yearBeginCompute, $monthBeginCompute, $dayBeginCompute, 0, 0, 0);
            $endDate = $beginDate->copy()->addMonth()->addDay();

            $dayOfWeekBegin = $beginDate->dayOfWeek;
            $dayOfWeekEnd = $endDate->dayOfWeek;
        }

        return [$beginDate, $endDate, $year, $month, $currentYear, $currentMonth];
    }

	/**
     * Searches checklist.
     *
     * @param boolean $refreshSession
     *
     * @return array
     */
    public function search($refreshSession=false) {
        list($beginDate, $endDate, $year, $month, $currentYear, $currentMonth) = $this->getDate(); 
        $conditions = [
            'beginDate' => $beginDate,
            'endDate' => $endDate,
            'errlist' => request()->errlist,
            'employeeId' => request()->employeeId,
            'employeeName' => request()->employeeName,
            'yearHistory'=>$year,
            'monthHistory'=>$month
        ];
        $search_history_conditions = $refreshSession?($this->getSession()?:$conditions):$conditions;
        $this->setSession($search_history_conditions);

        $checklists = $this->getCheckList($search_history_conditions);
        $totaldakoku = $checklists['checklists_timestamp_error']->count();
        $totalhyou = $checklists['checklists_confirm_needed']->count();

        $checklistsJson = [
            'beginDate' => $beginDate,
            'endDate' => $endDate,
            'checklists_timestamp_error'=> $checklists['checklists_timestamp_error'],
            'checklists_confirm_needed'=> $checklists['checklists_confirm_needed'],
            'totaldakoku'=>$totaldakoku,
            'totalhyou'=>$totalhyou
        ];
        $result = [
            'checklistsJson' => $checklistsJson,
            'currentMonth'=>$currentMonth,
            'currentYear'=>$currentYear,
            'checklistsHistory' => $search_history_conditions,
        ];

		return $result;
	}
}