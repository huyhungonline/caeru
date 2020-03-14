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
$factory->define(App\Manager::class, function (Faker\Generator $faker) {
    $jpFaker = \Faker\Factory::create('ja_JP');

    return [
        'presentation_id' => $faker->unique()->ean8(),
        'first_name' => $jpFaker->firstName(),
        'first_name_furigana' => $jpFaker->firstKanaName(),
        'last_name' => $jpFaker->lastName(),
        'last_name_furigana' => $jpFaker->lastKanaName(),
        'password' => 'solomon',
        'telephone' => $jpFaker->phoneNumber(),
        'email' => $jpFaker->safeEmail(),
        'enable' => 1,
        'company_wide_authority' => 1,

    ];
});
