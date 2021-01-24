<?php

use App\Banner;
use Illuminate\Database\Seeder;

class BannersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // slide
        Banner::create([
            'name' => 'Великолепное расположение',
            'description' => '',
            'button_text' => '',
            'type' => 'slide',
            'image' => 'banners/01.jpg',
            'url' => '#',
            'status' => '1',
        ]);
        Banner::create([
            'name' => 'Великолепное расположение 2',
            'description' => '',
            'button_text' => '',
            'type' => 'slide',
            'image' => 'banners/02.jpg',
            'url' => '#',
            'status' => '1',
        ]);

        // home
        Banner::create([
            'name' => '01',
            'description' => '',
            'button_text' => '',
            'type' => 'home_1',
            'image' => 'banners/home_01.jpg',
            'url' => '#',
            'status' => '1',
        ]);
        Banner::create([
            'name' => '02',
            'description' => '',
            'button_text' => '',
            'type' => 'home_2',
            'image' => 'banners/home_02.jpg',
            'url' => '#',
            'status' => '1',
        ]);
        Banner::create([
            'name' => '03',
            'description' => '',
            'button_text' => '',
            'type' => 'home_3',
            'image' => 'banners/home_03.jpg',
            'url' => '#',
            'status' => '1',
        ]);

        // middle
        Banner::create([
            'name' => 'Лучшие товары по доступным ценам',
            'description' => '',
            'button_text' => 'Перейти в каталог',
            'type' => 'middle_1',
            'image' => 'banners/middle_01.jpg',
            'url' => '#',
            'status' => '1',
        ]);
        Banner::create([
            'name' => 'Новинки',
            'description' => 'Летние скидки!',
            'button_text' => 'Перейти',
            'type' => 'middle_2',
            'image' => 'banners/middle_02.jpg',
            'url' => '#',
            'status' => '1',
        ]);
        Banner::create([
            'name' => 'Новинки',
            'description' => '',
            'button_text' => 'Перейти',
            'type' => 'middle_3',
            'image' => 'banners/middle_03.jpg',
            'url' => '#',
            'status' => '1',
        ]);
    }
}
