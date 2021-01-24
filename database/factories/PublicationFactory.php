<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Publication;
use App\Rubric;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Publication::class, function (Faker $faker) {
    $word = Str::title($faker->sentence);
    $rubric = Rubric::inRandomOrder()->first();
    $data = [
        'name' => $word,
        'slug' => Str::slug($word),
        'description' => $faker->paragraph,
        'body' => '<p>' . implode('</p><p>', $faker->paragraphs(6)) . '</p>',
        'status' => 1,
        'type' => 0, //mt_rand(0, 4),
        'rubric_id' => $rubric->id,
    ];

    return $data;
});
