<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default database, when a new company is registered.
        $this->call(TodofukensTableSeeder::class);
        $this->call(NationalHolidaysTableSeeder::class);
        $this->call(TimezonesTableSeeder::class);
        $this->call(DefaultCompanySeeder::class);
        $this->call(DefaultManagerSeeder::class);
        $this->call(DefaultSettingsSeeder::class);
        $this->call(DefaultWorkStatusesTableSeeder::class);
        $this->call(DefaultRestStatusesTableSeeder::class);
    }
}
