<?php

use Illuminate\Database\Seeder;
use App\Manager;

class DefaultManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new Manager([
            'presentation_id'               => 'admin',
            'password'                      => 'solomon',
            'first_name'                    => '名',
            'first_name_furigana'           => 'メイ',
            'last_name'                     => '姓',
            'last_name_furigana'            => 'セイ',
            'super'                         => true,
        ]);
        $admin->company()->associate(\App\Company::first());
        $admin->save();
        $admin->managerAuthority()->save(new \App\ManagerAuthority);
    }
}
