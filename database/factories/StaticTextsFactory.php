<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\StaticText;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(StaticText::class, function (Faker $faker) {
    $word = Str::ucfirst(implode(' ', $faker->words(2)));
    return [
        'name' => $word,
        'key' => Str::slug($word),
        'description' => $faker->sentence,
    ];
});
