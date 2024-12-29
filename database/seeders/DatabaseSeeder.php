<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\District;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Landlord;
use App\Models\Property;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
            District::factory()->count(10)->create();
        Property::factory()->count(10)->create();
        User::factory()->count(10)->create();
        Landlord::factory()->count(10)->create();


        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
