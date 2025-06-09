<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Offer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserMatch>
 */
class UserMatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'offer_id' => Offer::factory(),
            'user_referrer_id' => User::factory(),
            'user_referred_id' => function (array $attributes) {
                return User::where('id', '!=', $attributes['user_referrer_id'])
                    ->inRandomOrder()
                    ->first()
                    ->id;
            },
            'affiliate_link' => $this->faker->url(),
            'link_clicked' => $this->faker->boolean(0.25),
            'status' => $this->faker->randomElement(['open', 'in_progress', 'successful', 'unsuccessful', 'inactive']),
            'reason_unsuccessful_referrer' => $this->faker->paragraph(),
            'reason_unsuccessful_referred' => $this->faker->paragraph(),
        ];
    }
}
