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
        $offer = Offer::inRandomOrder()->first() ?? Offer::factory()->create();
        $applicant = User::where('id', '!=', $offer->user_id)->inRandomOrder()->first() ?? User::factory()->create();
        
        return [
            'offer_id' => $offer->id,
            'applicant_id' => $applicant->id,
            'offer_owner_id' => $offer->user_id,
            'message' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'is_read_by_applicant' => $this->faker->boolean(),
            'is_read_by_owner' => $this->faker->boolean(),
            'responded_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 week', 'now'),
        ];
    }

    public function pending(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'responded_at' => null,
            ];
        });
    }

    public function approved(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'responded_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            ];
        });
    }

    public function rejected(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
                'responded_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            ];
        });
    }
}