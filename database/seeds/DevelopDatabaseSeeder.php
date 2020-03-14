<?php

use Illuminate\Database\Seeder;

class DevelopDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Development database
        $this->call(TodofukensTableSeeder::class);
        $this->call(NationalHolidaysTableSeeder::class);
        $this->call(TimezonesTableSeeder::class);
        $this->call(CompaniesTableSeeder::class);
        $this->call(WorkLocationsTableSeeder::class);
        $this->call(DefaultSettingsSeeder::class);
        $this->call(WorkAddressesTableSeeder::class);
        $this->call(ManagersTableSeeder::class);
        $this->call(EmployeesTableSeeder::class);
        $this->call(DefaultWorkStatusesTableSeeder::class);
        $this->call(DefaultRestStatusesTableSeeder::class);
    }
}
