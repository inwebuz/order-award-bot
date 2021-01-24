<?php

use App\Rubric;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RubricsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('rubrics')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        factory(Rubric::class, 10)->create();
    }
}
