<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Category;
use App\Product;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Product::class, function (Faker $faker) {
    $wordCount = mt_rand(2, 8);
    $title = Str::title(implode(' ', $faker->words($wordCount)));
    $price = mt_rand(50, 500) * 1000;
    $imgNumber = mt_rand(1, 6);

    $product = [
        'name' => $title,
        'slug' => Str::slug($title),
        'description' => $faker->paragraph,
        'body' => '<p>' . implode('</p><p>', $faker->paragraphs(4)) . '</p>',
        'status' => 1,
        'price' => $price,
        'image' => 'temp/products/' . $imgNumber . '.png',
        'images' => '["temp//products//' . $imgNumber . '.png","temp//products//' . $imgNumber . '.png","temp//products//' . $imgNumber . '.png","temp//products//' . $imgNumber . '.png"]',
    ];

    return $product;
});
