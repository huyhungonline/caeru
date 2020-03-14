<?php

namespace App;

class WorkAddressWorkingInformation extends Model
{

    /**
     * Get the working day instance of this working information instance
     */
    public function workAddressWorkingDay()
    {
        return $this->belongsTo(WorkAddressWorkingDay::class);
    }

    /**
     * Get all the working employees of this work address working information
     */
    public function workAddressWorkingEmployees()
    {
        return $this->hasMany(WorkAddressWorkingEmployee::class);
    }

    /**
     * Get all the color statuses for this working information instance
     */
    public function colorStatuses()
    {
        return $this->hasMany(ColorStatus::class);
    }

    //// Accessor for the three attributes ////
    ///////////////////////////////////////////
    public function getPlannedStartWorkTimeAttribute($value)
    {
        return $this->getTimeDataFromMatchedPlannedSchedules('start_work_time', $value);
    }

    public function getPlannedEndWorkTimeAttribute($value)
    {
        return $this->getTimeDataFromMatchedPlannedSchedules('end_work_time', $value);
    }

    public function getCandidateNumberAttribute($value)
    {
        return $this->getPlannedCandidateNumber($value);
    }

    /**
     * Get the start_work_time and end_work_time from the PlannedSchedule through the WorkAddressWorkingEmployee instances
     *
     * @param $string   $field_name
     * @param mix       $value
     * @return mix
     */
    protected function getTimeDataFromMatchedPlannedSchedules($field_name, $value)
    {
        // In the case the value of this field is null
        if ($value === null) {

            // And in a WorkAddressWorkingInformation instance, all employees have the save start/end work times.
            // So we can get these info from the first, last or any employee of this instance.
            $first_employee = $this->workAddressWorkingEmployees->first();

            if ($first_employee) {
                return $first_employee->plannedSchedule->$field_name;
            } else {
                return null;
            }

        } else {
            return $value;
        }
    }

    /**
     * Get the planned candidate number of this WorkAddressWorkingInformation instance
     *
     * @param $string   $field_name
     * @param mix       $value
     * @return mix
     */
    protected function getPlannedCandidateNumber($value)
    {
        // If the instance does not have its own candidate number, choose the biggest from the employees's planned schedules
        if ($value === null) {

            // We have to count the employee number in the case of "All of the employee's planned schedules are fixed(固定)".
            // If that's the case, then the candidate number of this work information is fixed and is equal to the employee number.
            $current_employee_number = 0;
            // If there is one 'candidate' schedule (or some), then the candidate_number of this instance is
            // that schedule's candidate number (or the biggest candidate number in those schedules).
            $biggest_candidate_number = 0;
            $no_candidate_schedule = true;

            foreach ($this->workAddressWorkingEmployees as $employee) {

                $current_employee_number++;

                if ($employee->plannedSchedule->candidating_type == true) {
                    $biggest_candidate_number = $employee->plannedSchedule->candidate_number > $biggest_candidate_number ?
                        $employee->plannedSchedule->candidate_number :
                        $biggest_candidate_number;
                    $no_candidate_schedule = false;
                }
            }

            if ($no_candidate_schedule == true) {
                return $current_employee_number;
            } else {
                return $biggest_candidate_number;
            }

        } else {
            return $value;
        }
    }
}
