<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        factory(User::class)->create([
            'name' => 'Administrator',
            'email' => 'admin@nagrada.zipwolf.uz',
            'password' => bcrypt('T4d7n2YAeepGVC28'),
        ]);

        // factory(User::class)->create([
        //     'name' => 'Test',
        //     'email' => 'test@test.com',
        // ]);

//        factory(User::class, 100)->create();

    }
}
