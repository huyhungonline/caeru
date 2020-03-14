<?php

namespace App\Console\Commands;

use App\Company;
use App\EmployeeWorkingDay;
use App\ChecklistItem;
use Illuminate\Console\Command;
use App\Console\Commands\Reusables\DatabaseRelatedTrait;
use Carbon\Carbon;
use DB;

class CheckHaveScheduleButOfflineError extends Command
{
    use DatabaseRelatedTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caeru_error:check-have-schedule-but-offline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find any passed not-concluded-yet EmployeeWorkingDay that doesnt have any timestamp.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // At this moment the default database connection is still 'main'

        $companies = DB::table('companies')->get();

        foreach ($companies as $company) {

            $this->changeDatabaseConnectionTo($company->company_code);

            $change_date_moment = $this->getChangeDateMoment(Company::first());



            $potential_missed_working_days = EmployeeWorkingDay::with('employeeWorkingInformations.plannedSchedule',
                                                                        'employeeWorkingInformations.workAddressWorkingEmployee.plannedSchedule',
                                                                        'employeeWorkingInformations.workAddressWorkingEmployee.workAddressWorkingInformation')
                                                ->where('date', '<', $change_date_moment)
                                                ->notConcluded()->get();

            foreach ($potential_missed_working_days as $working_day) {
                
                $no_work_working_info = $working_day->haveANoWorkWorkingInformation();
                if ($no_work_working_info !== null) {

                    // Create an appropriate ChecklistItem entry
                    ChecklistItem::firstOrCreate([
                        'date'                              => $working_day->date,
                        'employee_id'                       => $working_day->employee_id,
                        'item_type'                         => ChecklistItem::CONFIRM_NEEDED,
                        'error_type'                        => ChecklistItem::HAVE_SCHEDULE_BUT_OFFLINE,
                        'employee_working_information_id'   => $no_work_working_info,
                    ]);
                }
            }
        }
    }

    /**
     * Get the change date moment from the setting of a given company
     *
     * @param Company   $compnay
     * @return string   'Y-m-d H:i:s'
     */
    protected function getChangeDateMoment($company)
    {
        $today = Carbon::now()->format('Y-m-d');
        
        if ($company->date_separate_type === Company::APPLY_TO_THE_DAY_BEFORE) {
            return $today . ' ' . $company->date_separate_time . ':00';
        } else {
            $previous_day = $today->subDay();
            return $previous_day->format('Y-m-d') . ' ' . $company->date_separate_time . ':00';
        }

    }
}
