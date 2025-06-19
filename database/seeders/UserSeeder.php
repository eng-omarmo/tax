<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'phone' => '123456789',
                'status' => 'active',
                'profile_image' => null,
                'role' => 'admin',
            ],
            [
                'name' => 'Tax Officer User',
                'email' => 'taxofficer@example.com',
                'password' => Hash::make('password'),
                'phone' => '321654987',
                'status' => 'active',
                'profile_image' => null,
                'role' => 'tax-officer',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::create($userData);
            $user->assignRole($role);
        }
    }
}
