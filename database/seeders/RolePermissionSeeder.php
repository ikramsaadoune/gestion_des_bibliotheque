<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'books.create', 'books.read', 'books.update', 'books.delete',
            'categories.create', 'categories.read', 'categories.update', 'categories.delete',
            'authors.create', 'authors.read', 'authors.update', 'authors.delete',
            'users.create', 'users.read', 'users.update', 'users.delete',
            'borrowings.manage',
            'reservations.manage',
            'fines.manage',
            'reviews.manage',
            'contacts.manage',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions);

        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $user->syncPermissions([
            'books.read',
            'categories.read',
            'authors.read',
        ]);
    }
}
