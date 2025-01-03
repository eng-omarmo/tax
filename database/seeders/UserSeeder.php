<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'), // Replace with a secure password
                'phone' => '123456789',
                'role' => 'Admin',
                'status' => 'active',
                'profile_image' => null,
            ],
            [
                'name' => 'Landlord User',
                'email' => 'landlord@example.com',
                'password' => Hash::make('password'), // Replace with a secure password
                'phone' => '987654321',
                'role' => 'Landlord',
                'status' => 'active',
                'profile_image' => null,
            ],
            [
                'name' => 'Business Owner User',
                'email' => 'businessowner@example.com',
                'password' => Hash::make('password'), // Replace with a secure password
                'phone' => '456789123',
                'role' => 'Business Owner',
                'status' => 'active',
                'profile_image' => null,
            ],
            [
                'name' => 'Tax Officer User',
                'email' => 'taxofficer@example.com',
                'password' => Hash::make('password'), // Replace with a secure password
                'phone' => '321654987',
                'role' => 'Tax Officer',
                'status' => 'active',
                'profile_image' => null,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
