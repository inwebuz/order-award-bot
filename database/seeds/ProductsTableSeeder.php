<?php

use App\Category;
use App\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        factory(Product::class)->create([
            'name' => 'Планка',
            'price' => 30000,
            'units' => 'секция|секции',
            'button_text' => 'Заказ планок',
        ]);
        factory(Product::class)->create([
            'name' => 'Колодка',
            'price' => 60000,
            'units' => 'секция|секции',
            'button_text' => 'Заказ колодок',
        ]);

    }
}
