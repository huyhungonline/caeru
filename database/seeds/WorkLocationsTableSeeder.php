<?php

use Illuminate\Database\Seeder;

class WorkLocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\WorkLocation::class)->states('head_office')->make()->company()->associate(\App\Company::first())->save();
        factory(App\WorkLocation::class,29)->states('branches')->make()->each(function ($w) {
            $w->company()->associate(\App\Company::first())->save();
        });
    }
}
