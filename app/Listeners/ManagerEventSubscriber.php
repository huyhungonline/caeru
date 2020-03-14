<?php

namespace App\Listeners;

use App\Events\ManagerSaved;
use Illuminate\Http\Request;
use App\IpAddress;

class ManagerEventSubscriber
{
    protected $request;

    /**
     * Create the event listener.
     *
     * @param Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            ManagerSaved::class,
            'App\Listeners\ManagerEventSubscriber@onManagerSaved'
        );
    }

    /**
     * Handle the 'saved' event of model manager
     *
     * @param ManagerCreated $vent
     * @return void
     */
    public function onManagerSaved(ManagerSaved $event)
    {
        // No need to do this when migrating with artisan or when log out
        if (!\App::runningInConsole() && ($this->request->route()->getName() != "logout")) {

            // If this is an update request:
            if (is_object($this->request->route('manager'))) {

                //reset the manager's ip addresses and authorized work locations lists
                $event->manager->ipAddresses()->detach();
                if (!$event->manager->company_wide_authority) {
                    $event->manager->workLocations()->detach();
                }

                // Update the authority of the manager
                $this->authorityAsssignation($event->manager->managerAuthority);

            } else { // If this is an make new request

                // Assign a new authority for the new manager
                $authority = new \App\ManagerAuthority();
                $authority->manager()->associate($event->manager);
                $this->authorityAsssignation($authority);

            }

            // Associate the new ip addresses list
            $ip_array = preg_split('/\s+/', $this->request->input('ip_address'));
            foreach ($ip_array as $address) {
                $event->manager->ipAddresses()->attach(IpAddress::firstOrCreate(['value' => $address]));
            }

            // In case this manager does not have the company wide authority, associate the authorized work locations list
            if (!$event->manager->company_wide_authority) {
                $event->manager->workLocations()->attach($this->request->input('authorized_work_locations'));
            }
        }
    }

    /**
     * Handle the authority part.(i.e. create new or update the authority model, etc.)
     *
     * @param ManagerAuthority $authority
     * @return ManagerAuthority $authority
     */
    protected function authorityAsssignation($authority)
    {
        $authority_fields = [
                'company_information',
                'work_location_information',
                'work_address_information',
                'employee_basic_information',
                'employee_work_information',
                'calendar_setting',
                'setting',
                'statuses_setting',
                'department_type_setting',
                'work_data_management',
                'work_data_search',
                'work_data_calculation',
                'work_data_detail',
                'work_data_personal_detail',
                'work_data_modify',
                'work_data_modify_request_confirm',
                'work_data_paid_holiday_management',
                'work_data_paid_holiday_detail',
                'work_data_addresses',
                'work_data_address_detail',
                'work_data_address_work_detail',
                'approval_level_one',
                'approval_level_two'
        ];

        $authority->fill($this->request->only($authority_fields));

        // These are true/false radio button fields, it's a little bit special
        $authority->work_data_modify = $this->request->input('work_data_modify') !== null;
        $authority->work_data_modify_request_confirm = $this->request->input('work_data_modify_request_confirm') !== null;

        $authority->save();

        return $authority;
    }
}
