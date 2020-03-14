<?php

namespace App\Listeners;


use Illuminate\Http\Request;
use App\Http\Controllers\SearchController;
use App\Events\CurrentWorkLocationChanged;
use App\Events\EmployeeInformationChanged;
use App\Events\WorkAddressInformationChanged;
use App\Events\EmployeeApprovalRelationshipChanged;
use App\Events\PlannedScheduleChanged;

class RegenerateSearchHistoryAndSaveSubscriber
{
    protected $controller;

    /**
     * Create the event listener.
     *
     * @param SearchController $controller
     * @return void
     */
    public function __construct(SearchController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        // Every time the Manager change the current work location in the session, we have to update the search histories again
        $events->listen(
            CurrentWorkLocationChanged::class,
            'App\Listeners\RegenerateSearchHistoryAndSaveSubscriber@updateEmployeeSearchHistory'
        );
        $events->listen(
            CurrentWorkLocationChanged::class,
            'App\Listeners\RegenerateSearchHistoryAndSaveSubscriber@updateWorkAddressSearchHistory'
        );

        // When Employee information changed, the employee's search history may change as well, that's why we need to update the search history again.
        //
        // Well, guess what? NOT ONLY the employee's search history may change, the work address's search history may change as well. Why? Because in
        // the work address search form, there are fields that are related to the employee's information. That's why we need to update both the search histories
        // of employee and work address.
        $events->listen(
            EmployeeInformationChanged::class,
            'App\Listeners\RegenerateSearchHistoryAndSaveSubscriber@updateEmployeeSearchHistory'
        );
        $events->listen(
            EmployeeInformationChanged::class,
            'App\Listeners\RegenerateSearchHistoryAndSaveSubscriber@updateWorkAddressSearchHistory'
        );

        // For the same reason as above, when the work address's information changed, update both the search histories of employee and work address.
        $events->listen(
            WorkAddressInformationChanged::class,
            'App\Listeners\RegenerateSearchHistoryAndSaveSubscriber@updateEmployeeSearchHistory'
        );
        $events->listen(
            WorkAddressInformationChanged::class,
            'App\Listeners\RegenerateSearchHistoryAndSaveSubscriber@updateWorkAddressSearchHistory'
        );

        // Woohoo, new flash: when the chief-subordinate relationships are changed, the search history of employee need to be updated as well.
        $events->listen(
            EmployeeApprovalRelationshipChanged::class,
            'App\Listeners\RegenerateSearchHistoryAndSaveSubscriber@updateEmployeeSearchHistory'
        );

        // Alright, alright, ok, you know what? When the friggin' PlannedSchedule are changed, we need to update the search histories of both employee
        // and work address as well. Note that this event is fired in all three actions of PlannedScheduleController: store (new), update, delete.
        $events->listen(
            PlannedScheduleChanged::class,
            'App\Listeners\RegenerateSearchHistoryAndSaveSubscriber@updateEmployeeSearchHistory'
        );
        $events->listen(
            PlannedScheduleChanged::class,
            'App\Listeners\RegenerateSearchHistoryAndSaveSubscriber@updateWorkAddressSearchHistory'
        );
    }

    /**
     * If there is already a search history in the session, and the user choose another work location, or change employee's information
     * that can lead to the result in history being out-of-date,
     * then we need to regenerate a new search history and save it to the session
     *
     * @param  CurrentWorkLocationChanged  $event
     * @return void
     */
    public function updateEmployeeSearchHistory()
    {
        $search_history = session('employee_search_history');

        if ($search_history) {

            $conditions = $search_history['conditions'];

            $this->controller->getEmployeesApplyConditionsSaveResultToSession($conditions);
        }
    }


    /**
     * If there is already a search history in the session, and the user choose another work location, or change employee's information
     * that can lead to the result in history being out-of-date,
     * then we need to regenerate a new search history and save it to the session
     *
     * @param  CurrentWorkLocationChanged  $event
     * @return void
     */
    public function updateWorkAddressSearchHistory()
    {
        $search_history = session('work_address_search_history');

        if ($search_history) {

            $conditions = $search_history['conditions'];

            $this->controller->getWorkAddressesApplyConditionsSaveResultToSession($conditions);
        }
    }

}
