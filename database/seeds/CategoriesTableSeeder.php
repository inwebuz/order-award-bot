<?php

use App\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

//        factory(Category::class, 10)->create();
//        factory(Category::class, 5)->create([
//           'parent_id' => Category::inRandomOrder()->first()->id,
//        ]);
//        factory(Category::class, 6)->create([
//           'parent_id' => Category::inRandomOrder()->first()->id,
//        ]);
//        factory(Category::class, 8)->create([
//           'parent_id' => Category::inRandomOrder()->first()->id,
//        ]);
//        factory(Category::class, 5)->create([
//           'parent_id' => Category::inRandomOrder()->first()->id,
//        ]);
//        factory(Category::class, 6)->create([
//           'parent_id' => Category::inRandomOrder()->first()->id,
//        ]);

    }
}
