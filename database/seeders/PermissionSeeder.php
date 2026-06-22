<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run()
    {
        DB::table('permissions')->delete();

        // Permission groups
        $permissions = [
            'Dashboard' => ['view'],
            'Products' => ['view', 'create', 'edit', 'delete'],
            'Orders' => ['view', 'update', 'delete'],
            'Users' => ['view', 'create', 'edit', 'delete'],
            'Roles' => ['view', 'create', 'edit', 'delete'],
            'Settings' => ['view', 'update'],
        ];

        foreach ($permissions as $group => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => strtolower($group) . '.' . $action,
                    'group_name' => $group,
                    'guard_name' => 'web',
                ]);
            }
        }

        // Default role
        $admin = Role::firstOrCreate(['name' => 'superadmin']);

        // Assign all permissions to superadmin
        $admin->syncPermissions(Permission::all());
    }}
