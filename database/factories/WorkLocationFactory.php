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
$factory->define(App\WorkLocation::class, function (Faker\Generator $faker) {
    $jpFaker = \Faker\Factory::create('ja_JP');

    return [
        'presentation_id' => $faker->unique()->ean8(),
        'registration_number' => $faker->unique()->numerify('########'),
        'name' => $jpFaker->prefecture(),
        'furigana' => $jpFaker->lastKanaName(),
        'postal_code' => $jpFaker->postcode(),
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

$factory->state(App\WorkLocation::class, 'head_office', function (Faker\Generator $faker) {
    
    $todofukens = DB::table('todofukens')->pluck('id', 'name')->toArray();
    $randomTodofuken = $faker->unique()->randomElement($todofukens);

    return [
        'name' => array_flip($todofukens)[$randomTodofuken] . '本社',
        'todofuken' => $randomTodofuken
    ];
});

$factory->state(App\WorkLocation::class, 'branches', function (Faker\Generator $faker) {
    
    $todofukens = DB::table('todofukens')->pluck('id', 'name')->toArray();
    $randomTodofuken = $faker->unique()->randomElement($todofukens);

    return [
        'name' => array_flip($todofukens)[$randomTodofuken] . '支社',
        'todofuken' => $randomTodofuken
    ];
});