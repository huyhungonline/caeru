<?php

use Illuminate\Database\Seeder;

class WorkAddressesTableSeeder extends Seeder
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

            $work_addresses = factory(App\WorkAddress::class, 5)->make();

            foreach ($work_addresses as $address) {
                $address->workLocation()->associate($work_location)->save();
                
                //TODO: create a planned schedule for each address. Right now, it's not neccessary, but will be in Phase 2 of the project.
            }
        }
    }
}
