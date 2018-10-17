<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB as DB;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\State::class, function (Faker $faker) {
    return [
        'name' => $faker->domainName,
        'abbr' => $faker->countryCode,
    ];
});