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
$factory->define(App\WorkAddress::class, function (Faker\Generator $faker) {
    $jpFaker = \Faker\Factory::create('ja_JP');
    $todofukens = DB::table('todofukens')->pluck('id', 'name')->toArray();
    $randomTodofuken = $faker->randomElement($todofukens);

    return [
        'presentation_id' => $faker->unique()->ean8(),
        'name' => $jpFaker->secondaryAddress(),
        'furigana' => $jpFaker->lastKanaName(),
        'postal_code' => $jpFaker->postcode(),
        'todofuken' => $randomTodofuken,
        'address' => $jpFaker->streetAddress(),
        'login_range' => 500,
        'latitude' => $jpFaker->latitude(),
        'longitude' => $jpFaker->longitude(),
        'telephone' => $jpFaker->phoneNumber(),
        'chief_first_name' => $jpFaker->firstName(),
        'chief_first_name_furigana' => $jpFaker->firstKanaName(),
        'chief_last_name' => $jpFaker->lastName(),
        'chief_last_name_furigana' => $jpFaker->lastKanaName(),
        'chief_email' => $jpFaker->safeEmail(),
    ];
});