<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;

class PropertiesTableSeeder extends Seeder
{
    public function run()
    {
        Property::factory()->count(10)->create();
    }
}
