<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // create roles and assign created permissions

        $role = Role::create(['name' => 'cow']);
        $role = Role::create(['name' => 'admin']);
        $role = Role::create(['name' => 'super-admin']);
        $role = Role::create(['name' => 'dev-admin']);
        $role = Role::create(['name' => 'contractor']);
    }

}
