<?php

use Illuminate\Database\Seeder;
use App\WorkStatus;

class DefaultWorkStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = \App\Company::first();

        DB::table('work_statuses')->insert([
            ['id' => WorkStatus::KEKKIN,    'name' =>  '欠勤', 'company_id' => $company->id,],
            ['id' => WorkStatus::FURIKYUU,  'name' =>  '振休', 'company_id' => $company->id,],
            ['id' => WorkStatus::FURIDE,    'name' =>  '振出', 'company_id' => $company->id,],
            ['id' => WorkStatus::HOUDE,     'name' =>  '法出', 'company_id' => $company->id,],
            ['id' => WorkStatus::KYUUDE,    'name' =>  '休出', 'company_id' => $company->id,],
        ]);
    }
}
