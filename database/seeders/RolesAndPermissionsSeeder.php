<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view login activities',

            // Property management
            'view properties',
            'create properties',
            'edit properties',
            'delete properties',

            // Landlord management
            'view landlords',
            'create landlords',
            'edit landlords',
            'delete landlords',

            // Location management
            'view locations',
            'create locations',
            'edit locations',
            'delete locations',

            // Unit management
            'view units',
            'create units',
            'edit units',
            'delete units',

            // Rent management
            'view rents',
            'create rents',
            'edit rents',
            'delete rents',

            // Tax management
            'view taxes',
            'create taxes',
            'edit taxes',
            'delete taxes',

            // Finance management
            'view finances',
            'manage finances',

            // Analytics
            'view monitoring',
            'view invoices',
            'view notifications',
            'manage notifications',

            // Reports
            'view reports',
            'generate reports',

            // Role & Permission management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'assign roles',
            'view permissions',
            'assign permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());

        $landlordRole = Role::firstOrCreate(['name' => 'Landlord']);
        $landlordRole->givePermissionTo([
            'view properties',
            'create properties',
            'edit properties',
            'view rents',
        ]);

        $taxOfficerRole = Role::firstOrCreate(['name' => 'Tax officer']);
        $taxOfficerRole->givePermissionTo([
            'view properties',
            'view landlords',
            'view rents',
            'view taxes',
            'create taxes',
            'edit taxes',
            'view monitoring',
            'view invoices',
            'view notifications',
            'view reports',
            'generate reports',
        ]);

        // Migrate existing users to the new role system
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role === 'Admin') {
                $user->assignRole('Admin');
            } elseif ($user->role === 'lanlord' || $user->role === 'Landlord') {
                $user->assignRole('Landlord');
            } elseif ($user->role === 'Tax officer') {
                $user->assignRole('Tax officer');
            }
        }
    }
}
