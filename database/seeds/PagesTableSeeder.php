<?php

use App\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $page = factory(Page::class)->create([
            'name' => 'Garden Plaza Бухара',
            'slug' => 'home',
            'order' => 1,
            'show_in' => 0,
        ]);
        $page = $page->translate('uz');
        $page->name = 'Garden Plaza Buxoro';
        $page->save();
        $page = $page->translate('en');
        $page->name = 'Garden Plaza Bukhara';
        $page->save();

        $page = factory(Page::class)->create([
            'name' => 'Контакты',
            'slug' => 'contacts',
            'order' => 1000,
            'show_in' => 0,
        ]);
        $page = $page->translate('uz');
        $page->name = 'Aloqa';
        $page->save();
        $page = $page->translate('en');
        $page->name = 'Contacts';
        $page->save();

        $page = factory(Page::class)->create([
            'name' => 'О нас',
            'slug' => 'about',
            'order' => 990,
            'show_in' => 0,
        ]);
        $page = $page->translate('uz');
        $page->name = 'Biz haqimizda';
        $page->save();
        $page = $page->translate('en');
        $page->name = 'About us';
        $page->save();

        $page = factory(Page::class)->create([
            'name' => 'Номера',
            'slug' => 'rooms',
            'order' => 20,
            'show_in' => 1,
        ]);
        $page = $page->translate('uz');
        $page->name = 'Xonalar';
        $page->save();
        $page = $page->translate('en');
        $page->name = 'Rooms';
        $page->save();

        $page = factory(Page::class)->create([
            'name' => 'Достопримечательности',
            'slug' => 'attractions',
            'order' => 30,
            'show_in' => 1,
        ]);
        $page = $page->translate('uz');
        $page->name = 'Area Attractions';
        $page->save();
        $page = $page->translate('en');
        $page->name = 'Area Attractions';
        $page->save();

        $page = factory(Page::class)->create([
            'name' => 'Рестораны',
            'slug' => 'restaurants',
            'order' => 40,
            'show_in' => 1,
        ]);
        $page = $page->translate('uz');
        $page->name = 'Restoranlar';
        $page->save();
        $page = $page->translate('en');
        $page->name = 'Restaurants';
        $page->save();

        $page = factory(Page::class)->create([
            'name' => 'Мероприятия',
            'slug' => 'special-events',
            'order' => 50,
            'show_in' => 1,
        ]);
        $page = $page->translate('uz');
        $page->name = 'Tadbirlar';
        $page->save();
        $page = $page->translate('en');
        $page->name = 'Special Events';
        $page->save();

        $page = factory(Page::class)->create([
            'name' => 'Галерея',
            'slug' => 'gallery',
            'order' => 60,
            'show_in' => 1,
        ]);
        $page = $page->translate('uz');
        $page->name = 'Galereya';
        $page->save();
        $page = $page->translate('en');
        $page->name = 'Gallery';
        $page->save();

//        $page = factory(Page::class)->create([
//            'name' => 'Каталог',
//            'slug' => 'catalogue',
//            'order' => 20,
//            'show_in' => 1,
//        ]);
//        $page = $page->translate('uz');
//        $page->name = 'Katalog';
//        $page->save();

//        $page = factory(Page::class)->create([
//            'name' => 'Новости',
//            'slug' => 'news',
//            'order' => 60,
//            'show_in' => 1,
//        ]);
//        $page = $page->translate('uz');
//        $page->name = 'Yangiliklar';
//        $page->save();


//        $page = factory(Page::class)->create([
//            'name' => 'Частые вопросы',
//            'slug' => Str::slug('Частые вопросы'),
//            'order' => 70,
//            'show_in' => 2,
//        ]);
//        $page = $page->translate('uz');
//        $page->name = 'FAQ';
//        $page->save();

//        $page = factory(Page::class)->create([
//            'name' => 'Публичная офферта',
//            'slug' => Str::slug('Публичная офферта'),
//            'order' => 80,
//            'show_in' => 2,
//        ]);
//        $page = $page->translate('uz');
//        $page->name = 'Oferta';
//        $page->save();

//        $page = factory(Page::class)->create([
//            'name' => 'Условия доставки',
//            'slug' => Str::slug('Условия доставки'),
//            'order' => 90,
//            'show_in' => 2,
//        ]);
//        $page = $page->translate('uz');
//        $page->name = 'Yetkazib berish';
//        $page->save();

//        $page = factory(Page::class)->create([
//            'name' => 'Условия оплаты',
//            'slug' => Str::slug('Условия оплаты'),
//            'order' => 100,
//            'show_in' => 2,
//        ]);
//        $page = $page->translate('uz');
//        $page->name = 'To‘lov shartlari';
//        $page->save();

//        $page = factory(Page::class)->create([
//            'name' => 'Магазины',
//            'slug' => Str::slug('Магазины'),
//            'order' => 110,
//            'show_in' => 2,
//        ]);
//        $page = $page->translate('uz');
//        $page->name = 'Magazinlar';
//        $page->save();

    }
}
