<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {

        return [
            'offer_id' => Offer::factory(),
            'applicant_id' => User::factory(),
            'offer_owner_id' => function (array $attributes) {
                return User::where('id', '!=', $attributes['applicant_id'])
                    ->inRandomOrder()
                    ->first()
                    ->id;
            },
            'message' => $this->faker->paragraph(),
            'is_read_by_applicant' => $this->faker->boolean(),
            'is_read_by_owner' => $this->faker->boolean(),
            'is_archived_by_applicant' => $this->faker->boolean(0.1),
            'is_archived_by_owner' => $this->faker->boolean(0.2),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'retracted']),
            'responded_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 week', 'now'),
        ];
    }

}
