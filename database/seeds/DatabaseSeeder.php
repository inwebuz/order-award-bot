<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // DataTypesTableSeeder::class,
            // DataRowsTableSeeder::class,
            // MenusTableSeeder::class,
            // MenuItemsTableSeeder::class,
            // RolesTableSeeder::class,
            // PermissionsTableSeeder::class,
            // PermissionRoleTableSeeder::class,

            // SettingsTableSeeder::class,

            UsersTableSeeder::class,
            // PagesTableSeeder::class,
            // StaticTextsTableSeeder::class,

            // BannersTableSeeder::class,
            // CategoriesTableSeeder::class,
            // RubricsTableSeeder::class,

            ProductsTableSeeder::class,
            // PublicationsTableSeeder::class,

            TranslationsTableSeeder::class,

        ]);
    }
}
