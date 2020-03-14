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
$factory->define(App\Employee::class, function (Faker\Generator $faker) {
    $jpFaker = \Faker\Factory::create('ja_JP');
    $todofukens = DB::table('todofukens')->pluck('id', 'name')->toArray();
    $randomTodofuken = $faker->randomElement($todofukens);

    return [
        'presentation_id' => $faker->unique()->ean8(),
        'first_name' => $jpFaker->firstName(),
        'first_name_furigana' => $jpFaker->firstKanaName(),
        'last_name' => $jpFaker->lastName(),
        'last_name_furigana' => $jpFaker->lastKanaName(),
        'birthday' => $faker->dateTimeBetween('-40 years', '-20 years')->format('Y-m-d'),
        'gender' => ($faker->randomDigit()%2) + 1,
        'postal_code' => $jpFaker->postcode(),
        'todofuken' => $randomTodofuken,
        'address' => $jpFaker->streetAddress(),
        'telephone' => $jpFaker->phoneNumber(),
        'email' => $jpFaker->safeEmail(),
        'joined_date' => $faker->dateTimeBetween('-15 years', 'now')->format('Y-m-d'),
        'schedule_type' => config('constants.normal_schedule'),
        'employment_type' => config('constants.official_employee'),
        'salary_type' => config('constants.monthly_salary'),
        'work_status' => config('constants.working'),
        'card_registration_number' => $faker->unique()->numerify('########'),
    ];
});
