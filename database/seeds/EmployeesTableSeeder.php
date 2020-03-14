<?php

use Illuminate\Database\Seeder;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $work_locations = \App\WorkLocation::all();

        foreach ($work_locations as $work_location) {

            $employees = factory(App\Employee::class, 10)->make();
            
            foreach ($employees as $employee) {
                $employee->workLocation()->associate($work_location)->save();

                //TODO: create a planned schedule for each employee. Right now, it's not neccessary, but will be in Phase 2 of the project.
            }
            
        }
    }
}
