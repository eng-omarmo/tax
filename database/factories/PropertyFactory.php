<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition()
    {
        return [
            'property_name' => $this->faker->streetName,
            'property_phone' => $this->faker->phoneNumber,
            'house_code' => $this->faker->unique()->numberBetween(10000, 99999),
            'branch' => $this->faker->city,
            'district_id' => District::factory(),
            'zone' => $this->faker->word,
            'nbr' => $this->faker->word . $this->faker->numberBetween(1, 10),
            'branch' => $this->faker->city,
            'designation' => $this->faker->jobTitle,
            'house_type' => $this->faker->word,
            'house_rent' => $this->faker->numberBetween(1000, 10000),
            'quarterly_tax_fee' => $this->faker->numberBetween(500, 3000),
            'yearly_tax_fee' => $this->faker->numberBetween(2000, 12000),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'dalal_company_name' => $this->faker->company,
            'monitoring_status' => $this->faker->randomElement(['Pending', 'Approved']),
            'status' => $this->faker->randomElement(['Active', 'Inactive']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
