<?php

namespace App\Policies;

use App\Manager;
use App\ManagerAuthority;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuthorityPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the current manager can change managers information
     *
     * @param Manager $manager
     * @return boolean
     */
    public function changeManagerInformation(Manager $manager)
    {
        return $manager->super;
    }

    /**
     * Determine if the current manager can change company information
     *
     * @param Manager $manager
     * @return boolean
     */
    public function changeCompanyInformation(Manager $manager)
    {
        return $manager->managerAuthority->company_information == ManagerAuthority::CHANGE;
    }

    /**
     * Determine if the current manager can view company information
     *
     * @param Manager $manager
     * @return boolean
     */
    public function viewCompanyInformation(Manager $manager)
    {
        return $manager->managerAuthority->company_information != ManagerAuthority::NOTHING;
    }

    /**
     * Determine if the current manager can change/add work location
     *
     * @param Manager $manager
     * @return boolean
     */
    public function changeWorkLocationInformation(Manager $manager)
    {
        return $manager->managerAuthority->work_location_information == ManagerAuthority::CHANGE;
    }

    /**
     * Determine if the current manager can view work location
     *
     * @param Manager $manager
     * @return boolean
     */
    public function viewWorkLocationInformation(Manager $manager)
    {
        return $manager->managerAuthority->work_location_information != ManagerAuthority::NOTHING;
    }

    /**
     * Determine if the current manager can change/add work address
     *
     * @param Manager $manager
     * @return boolean
     */
    public function changeWorkAddressInformation(Manager $manager)
    {
        return $manager->managerAuthority->work_address_information == ManagerAuthority::CHANGE;
    }

    /**
     * Determine if the current manager can view work address
     *
     * @param Manager $manager
     * @return boolean
     */
    public function viewWorkAddressInformation(Manager $manager)
    {
        return $manager->managerAuthority->work_address_information != ManagerAuthority::NOTHING && $manager->company->use_address_system == true;
    }

    /**
     * Determine if the current manager can change this work address's work location at that moment
     *
     * @param Manager $manager
     * @return boolean
     */
    public function changeWorkAddressWorkLocationInformation(Manager $manager)
    {
        return ($this->changeWorkAddressInformation($manager)) && !is_numeric(session('current_work_location'));
    }

    /**
     * Determine if the current manager can change/add employee's basic information
     *
     * @param Manager $manager
     * @return boolean
     */
    public function changeEmployeeBasicInformation(Manager $manager)
    {
        return ($manager->company_wide_authority) ? ($manager->managerAuthority->employee_basic_information == ManagerAuthority::CHANGE) :
        ($manager->managerAuthority->employee_basic_information == ManagerAuthority::CHANGE &&
            ($manager->workLocations->contains('id', session('current_work_location')) || $this->containsAllWorklocations($manager, session('current_work_location')))
        );
    }

    /**
     * Determine if the current manager can view employee's basic information
     *
     * @param Manager $manager
     * @return boolean
     */
    public function viewEmployeeBasicInformation(Manager $manager)
    {
        return ($manager->company_wide_authority) ? ($manager->managerAuthority->employee_basic_information != ManagerAuthority::NOTHING) :
        ($manager->managerAuthority->employee_basic_information != ManagerAuthority::NOTHING &&
            ($manager->workLocations->contains('id', session('current_work_location')) || $this->containsAllWorklocations($manager, session('current_work_location')))
        );
    }

    /**
     * Determine if the current manager can change employee's work location at that moment
     *
     * @param Manager $manager
     * @return boolean
     */
    public function changeEmployeeWorkLocationInformation(Manager $manager)
    {
        return ($this->changeEmployeeBasicInformation($manager)) && !is_numeric(session('current_work_location'));
    }

    /**
     * Determine if the current manager can change/add employee's work information
     *
     * @param Manager $manager
     * @return boolean
     */
    public function changeEmployeeWorkInformation(Manager $manager)
    {
        return ($manager->company_wide_authority) ? ($manager->managerAuthority->employee_work_information == ManagerAuthority::CHANGE) :
        ($manager->managerAuthority->employee_work_information == ManagerAuthority::CHANGE &&
            ($manager->workLocations->contains('id', session('current_work_location')) || $this->containsAllWorklocations($manager, session('current_work_location')))
        );
    }

    /**
     * Determine if the current manager can view employee's work information
     *
     * @param Manager $manager
     * @return boolean
     */
    public function viewEmployeeWorkInformation(Manager $manager)
    {
        return ($manager->company_wide_authority) ? ($manager->managerAuthority->employee_work_information != ManagerAuthority::NOTHING) :
        ($manager->managerAuthority->employee_work_information != ManagerAuthority::NOTHING &&
            ($manager->workLocations->contains('id', session('current_work_location')) || $this->containsAllWorklocations($manager, session('current_work_location')))
        );
    }

    /**
     * Determine if the current manager can see the tab employee on the navigation
     *
     * @param Manager $manager
     * @return boolean
     */
    public function seeEmployeeTabInNavigationBar(Manager $manager)
    {
        return (($manager->managerAuthority->employee_basic_information != ManagerAuthority::NOTHING) ||
            ($manager->managerAuthority->employee_work_information != ManagerAuthority::NOTHING));
    }

    /**
     * Determine if the current manager can change calendar information
     *
     * @param Manager $manager
     * @return boolean
     */
    public function changeCalendarInformation(Manager $manager)
    {
        return $manager->managerAuthority->calendar_setting == ManagerAuthority::CHANGE;
    }

    /**
     * Determine if the current manager can view calendar information
     *
     * @param Manager $manager
     * @return boolean
     */
    public function viewCalendarInformation(Manager $manager)
    {
        return $manager->managerAuthority->calendar_setting != ManagerAuthority::NOTHING;
    }

    /**
     * Determine if the current manager can change setting information
     *
     * @param Manager $manager
     * @return boolean
     */
    public function changeSettingInformation(Manager $manager)
    {
        return ($manager->company_wide_authority) ? ($manager->managerAuthority->setting == ManagerAuthority::CHANGE) :
        ($manager->managerAuthority->setting == ManagerAuthority::CHANGE &&
            ($manager->workLocations->contains('id', session('current_work_location')) || $this->containsAllWorklocations($manager, session('current_work_location')))
        );
    }

    /**
     * Determine if the current manager can view setting information
     *
     * @param Manager $manager
     * @return boolean
     */
    public function viewSettingInformation(Manager $manager)
    {
        return ($manager->company_wide_authority) ? ($manager->managerAuthority->setting != ManagerAuthority::NOTHING) :
        ($manager->managerAuthority->setting != ManagerAuthority::NOTHING &&
            ($manager->workLocations->contains('id', session('current_work_location')) || $this->containsAllWorklocations($manager, session('current_work_location')))
        );
    }

    /**
     * Determine if the current manager can change statuses setting information
     *
     * @param Manager $manager
     * @return boolean
     */
    public function changeStatusesSettingInformation(Manager $manager)
    {
        return $manager->managerAuthority->statuses_setting == ManagerAuthority::CHANGE;
    }

    /**
     * Determine if the current manager can view statuses setting information
     *
     * @param Manager $manager
     * @return boolean
     */
    public function viewStatusesSettingInformation(Manager $manager)
    {
        return $manager->managerAuthority->statuses_setting != ManagerAuthority::NOTHING;
    }

    /**
     * Determine if the current manager can change statuses setting information
     *
     * @param Manager $manager
     * @return boolean
     */
    public function changeDepartmentTypeSettingInformation(Manager $manager)
    {
        return $manager->managerAuthority->department_type_setting == ManagerAuthority::CHANGE;
    }

    /**
     * Determine if the current manager can view statuses setting information
     *
     * @param Manager $manager
     * @return boolean
     */
    public function viewDepartmentTypeSettingInformation(Manager $manager)
    {
        return $manager->managerAuthority->department_type_setting != ManagerAuthority::NOTHING;
    }

    /**
     * Determine if the current manager can view option item's page
     *
     * @param Manager $manager
     * @return boolean
     */
    public function viewOptionItemInformation(Manager $manager)
    {
        return ($manager->managerAuthority->department_type_setting != ManagerAuthority::NOTHING || $manager->managerAuthority->statuses_setting != ManagerAuthority::NOTHING);
    }

    /**
     * Determine if the current manager can view employee_working_day page of the attendance part
     *
     * @param Manager $manager
     * @return boolean
     */
    public function viewAttendanceEmployeeWorkingDay(Manager $manager)
    {
        return $manager->managerAuthority->work_data_detail == ManagerAuthority::BROWSE;
    }

    /**
     * Determine if the current manager can change employee's working data
     *
     * @param Manager $manager
     * @return boolean
     */
    public function changeAttendanceData(Manager $manager)
    {
        return $manager->managerAuthority->work_data_modify == true;
    }

    /**
     * An utility function to determine if all the work locations in the list is in the manager's authority
     *
     * @param Manager   $manager
     * @param array     $work_locations
     * @return boolean
     */
    private function containsAllWorklocations($manager, $work_locations)
    {
        return collect($work_locations)->every(function($work_location) use ($manager) {
            return $manager->workLocations->contains('id', $work_location);
        });
    }

}
