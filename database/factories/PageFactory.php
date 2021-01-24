<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Page;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Page::class, function (Faker $faker) {
    $word = Str::title($faker->word);
    return [
        'name' => $word,
        'slug' => Str::slug($word),
        'description' => $faker->text(),
        'body' => '<p>' . implode('</p><p>', $faker->paragraphs(4)) . '</p>',
        'status' => 1,
    ];
});
