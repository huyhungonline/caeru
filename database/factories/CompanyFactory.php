<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Company::class, function (Faker\Generator $faker) {
    $jpFaker = \Faker\Factory::create('ja_JP');

    $todofukens = DB::table('todofukens')->pluck('id', 'name')->toArray();
    $randomTodofuken = $faker->randomElement($todofukens);

    return [
        'code' => 'itz',
        'name' => $jpFaker->company(),
        'furigana' => $jpFaker->lastKanaName(),
        'postal_code' => $jpFaker->postcode(),
        'todofuken' => $randomTodofuken,
        'address' => $jpFaker->streetAddress(),
        'telephone' => $jpFaker->phoneNumber(),
        'fax' => $jpFaker->phoneNumber(),
        'ceo_first_name' => $jpFaker->firstName(),
        'ceo_first_name_furigana' => $jpFaker->firstKanaName(),
        'ceo_last_name' => $jpFaker->lastName(),
        'ceo_last_name_furigana' => $jpFaker->lastKanaName(),
        'ceo_email' => $jpFaker->safeEmail(),
        'billing_person_first_name' => $jpFaker->firstName(),
        'billing_person_first_name_furigana' => $jpFaker->firstKanaName(),
        'billing_person_last_name' => $jpFaker->lastName(),
        'billing_person_last_name_furigana' => $jpFaker->lastKanaName(),
        'billing_person_email' => $jpFaker->safeEmail(),
        'date_separate_time' => '00:00:00',
        'date_separate_type' => head(array_keys(config('caeru.date_separate_types')))
    ];
});
