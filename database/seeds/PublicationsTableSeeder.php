<?php

use App\Publication;
use App\Pubrubric;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublicationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('publications')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        factory(Publication::class, 10)->create([
            'user_id' => 3,
        ]);
        factory(Publication::class, 50)->create();
    }
}
