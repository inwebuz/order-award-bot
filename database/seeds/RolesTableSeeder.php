<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        $role = Role::firstOrNew(['name' => 'admin']);
        if (!$role->exists) {
            $role->fill([
                'display_name' => __('seeders.roles.admin'),
            ])->save();
        }

        $role = Role::firstOrNew(['name' => 'administrator']);
        if (!$role->exists) {
            $role->fill([
                'display_name' => __('seeders.roles.administrator'),
            ])->save();
        }

        $role = Role::firstOrNew(['name' => 'seller']);
        if (!$role->exists) {
            $role->fill([
                'display_name' => __('seeders.roles.seller'),
            ])->save();
        }

        $role = Role::firstOrNew(['name' => 'user']);
        if (!$role->exists) {
            $role->fill([
                'display_name' => __('seeders.roles.user'),
            ])->save();
        }
    }
}
