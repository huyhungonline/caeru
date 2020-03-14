<?php

use Illuminate\Database\Seeder;

class DefaultCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Company::create([
            'name'      => '株式会社ITZ',
            'furigana'  => 'カブシキガイシャアイティゼット',
            'date_separate_type' => head(array_keys(config('caeru.date_separate_types'))),
            'use_address_system'    => false,
        ]);
    }
}
