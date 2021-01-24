<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;

class PermissionRoleTableSeeder extends Seeder
{
    protected $onlySuperAdminTables = [
        'menus', 'roles',
    ];
    protected $onlySuperAdminPermissions = [
        'browse_bread', 'browse_database', 'browse_compass', 'browse_hooks', 'add_pages', 'delete_pages', 'add_static_texts', 'delete_static_texts',
    ];
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        // Super Administrator
        $role = Role::where('name', 'admin')->firstOrFail();
        $permissions = Permission::all();
        $role->permissions()->sync(
            $permissions->pluck('id')->all()
        );

        // Administrator
        $role = Role::where('name', 'administrator')->firstOrFail();
        $permissions = Permission::where(function ($query) {
            $query->whereNotIn('table_name', $this->onlySuperAdminTables)->orWhereNull('table_name');
        })->whereNotIn('key', $this->onlySuperAdminPermissions);
        $role->permissions()->sync(
            $permissions->pluck('id')->all()
        );
    }
}
