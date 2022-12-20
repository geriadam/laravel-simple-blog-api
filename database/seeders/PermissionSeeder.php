<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // reset cahced roles and permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $posts = [
            "Posts - Index",
            "Posts - Create",
            "Posts - Update",
            "Posts - Delete",
            "Posts - Show",
        ];

        $users = [
            "Users - Index",
            "Users - Create",
            "Users - Update",
            "Users - Delete",
            "Users - Show",
        ];

        $permissions = array_merge($posts, $users);

        // create permissions
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        $managerRole = Role::updateOrCreate(['name' => User::USER_ROLE_MANAGER, 'guard_name' => 'api']);
        $managerRole->givePermissionTo($posts);

        $writerRole = Role::updateOrCreate(['name' => User::USER_ROLE_WRITER, 'guard_name' => 'api']);
        $writerRole->givePermissionTo($posts);

        $adminRole = Role::create(['name' => User::USER_ROLE_ADMIN, 'guard_name' => 'api']);

        // create demo users
        $user = User::factory()->create([
            'name' => 'Manager',
            'email' => 'manager@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $user->assignRole($managerRole);

        $user = User::factory()->create([
            'name' => 'Writer1',
            'email' => 'writer1@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $user->assignRole($writerRole);

        $user = User::factory()->create([
            'name' => 'Writer2',
            'email' => 'writer2@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $user->assignRole($writerRole);

        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('12345678')
        ]);
        $user->assignRole($adminRole);
    }
}
