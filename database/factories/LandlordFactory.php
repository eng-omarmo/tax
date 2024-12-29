<?php

namespace Database\Factories;

use App\Models\Landlord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LandlordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Landlord::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'user_id' => User::factory(),  // Using a factory to generate a related user.
        ];
    }
}
