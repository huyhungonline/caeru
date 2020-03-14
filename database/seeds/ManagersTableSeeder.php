<?php

use Illuminate\Database\Seeder;

class ManagersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = factory(App\Manager::class)->make(['presentation_id' => 'admin', 'super' => true])->company()->associate(\App\Company::first());
        $admin->save();
        $admin->managerAuthority()->save(new \App\ManagerAuthority);

        $managers = factory(App\Manager::class, 29)->make();
        foreach ($managers as $manager) {
            $manager->company()->associate(\App\Company::first());
            $manager->save();
            $manager->managerAuthority()->save(new \App\ManagerAuthority);
        }
    }
}
