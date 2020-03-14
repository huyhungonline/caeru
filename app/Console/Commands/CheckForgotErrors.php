<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ChecklistErrorTimer;
use App\ChecklistItem;
use Carbon\Carbon;
use App\Console\Commands\Reusables\DatabaseRelatedTrait;

class CheckForgotErrors extends Command
{
    use DatabaseRelatedTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caeru_error:check-forgot-errors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if there is any ChecklistErrorTimer has passed due to perform clean-up';

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
        $right_now = Carbon::now();
        $right_now->second = 0;

        $passed_due_timers = ChecklistErrorTimer::where('due_time', '<', $right_now->format('Y-m-d H:i:s'))->get();

        foreach ($passed_due_timers as $timer) {
            
            // Change to the database of this timer record's company
            $this->changeDatabaseConnectionTo($timer->company_code);

            // Create an appropriate ChecklistItem entry for this timer
            ChecklistItem::firstOrCreate([
                'date'                              => $timer->date,
                'employee_id'                       => $timer->employee_id,
                'item_type'                         => ChecklistItem::TIMESTAMP_ERROR,
                'error_type'                        => $timer->timestamp_error_type,
                'employee_working_information_id'   => $timer->working_info_id,
            ]);

            // Remove the timer
            $timer->delete();

        }
    }
}
