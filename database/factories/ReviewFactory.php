<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use App\Review;
use App\User;
use Faker\Generator as Faker;

$factory->define(Review::class, function (Faker $faker) {
    $wordCount = mt_rand(1, 2);
    $title = Str::title(implode(' ', $faker->words($wordCount)));
    $product = Product::inRandomOrder()->first();
    $hasUser = mt_rand(0, 1);
    $review = [
        'name' => $title,
        'body' => $faker->paragraph,
        'status' => 1,
        'rating' => mt_rand(1, 5),
        'reviewable_type' => 'App\Product',
        'reviewable_id' => $product->id,
        'user_id' => ($hasUser) ? User::inRandomOrder()->first() : null,
    ];
    return $review;
});
