<?php

use Illuminate\Database\Seeder;

class DefaultOptionItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = \App\Company::first();

        DB::table('option_items')->insert([
            [
            'company_id' => $company->id,
            'type' => \App\OptionItem::REST_STATUS,
            'name' => '有給',
            ],

            [
            'company_id' => $company->id,
            'type' => \App\OptionItem::REST_STATUS,
            'name' => '有休',
            ],

            [
            'company_id' => $company->id,
            'type' => \App\OptionItem::REST_STATUS,
            'name' => '前給',
            ],

            [
            'company_id' => $company->id,
            'type' => \App\OptionItem::REST_STATUS,
            'name' => '前休',
            ],

            [
            'company_id' => $company->id,
            'type' => \App\OptionItem::REST_STATUS,
            'name' => '後給',
            ],

            [
            'company_id' => $company->id,
            'type' => \App\OptionItem::REST_STATUS,
            'name' => '後休',
            ],

            [
            'company_id' => $company->id,
            'type' => \App\OptionItem::REST_STATUS,
            'name' => '時有',
            ],

            [
            'company_id' => $company->id,
            'type' => \App\OptionItem::REST_STATUS,
            'name' => '半給',
            ],

            [
            'company_id' => $company->id,
            'type' => \App\OptionItem::REST_STATUS,
            'name' => '半休',
            ],
            
            [
            'company_id' => $company->id,
            'type' => \App\OptionItem::WORK_STATUS,
            'name' => '欠勤',
            ],

            [
            'company_id' => $company->id,
            'type' => \App\OptionItem::WORK_STATUS,
            'name' => '振休',
            ],

            [
            'company_id' => $company->id,
            'type' => \App\OptionItem::WORK_STATUS,
            'name' => '振出',
            ],

            [
            'company_id' => $company->id,
            'type' => \App\OptionItem::WORK_STATUS,
            'name' => '法出',
            ],

            [
            'company_id' => $company->id,
            'type' => \App\OptionItem::WORK_STATUS,
            'name' => '休出',
            ],

        ]);
    }
}
