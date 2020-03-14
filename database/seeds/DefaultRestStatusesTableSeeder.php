<?php

use Illuminate\Database\Seeder;
use App\RestStatus;

class DefaultRestStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = \App\Company::first();

        DB::table('rest_statuses')->insert([
            ['id' => RestStatus::YUUKYU_1,    'name' =>   '有給', 'company_id' => $company->id, 'unit_type' =>    1, 'paid_type' => 1,],
            ['id' => RestStatus::YUUKYU_2,    'name' =>   '有休', 'company_id' => $company->id, 'unit_type' =>    1, 'paid_type' => 1,],
            ['id' => RestStatus::ZENKYUU_1,   'name' =>   '前給', 'company_id' => $company->id, 'unit_type' =>    0, 'paid_type' => 1,],
            ['id' => RestStatus::ZENKYUU_2,   'name' =>   '前休', 'company_id' => $company->id, 'unit_type' =>    0, 'paid_type' => 1,],
            ['id' => RestStatus::GOKYUU_1,    'name' =>   '後給', 'company_id' => $company->id, 'unit_type' =>    0, 'paid_type' => 1,],
            ['id' => RestStatus::GOKYUU_2,    'name' =>   '後休', 'company_id' => $company->id, 'unit_type' =>    0, 'paid_type' => 1,],
            ['id' => RestStatus::JIYUU,       'name' =>   '時有', 'company_id' => $company->id, 'unit_type' =>    0, 'paid_type' => 1,],
            ['id' => RestStatus::HANKYUU_1,   'name' =>   '半給', 'company_id' => $company->id, 'unit_type' =>    0, 'paid_type' => 1,],
            ['id' => RestStatus::HANKYUU_2,   'name' =>   '半休', 'company_id' => $company->id, 'unit_type' =>    0, 'paid_type' => 1,],
        ]);
    }
}
