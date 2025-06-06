<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Offer>
 */
final class OfferFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Offer::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            // 'id' => Str::uuid(),
            'offered_by_type' => $this->faker->randomElement(['referrer', 'referred']),
            'user_id' => \App\Models\User::factory(),
            'company_id' => \App\Models\Company::factory(),
            'offer_title' => $this->faker->sentence(6, true),
            'offer_description' => $this->faker->paragraph(4, true),
            'reward_total_cents' => $this->faker->numberBetween(1000, 10000),
            'reward_split_referrer_percent' => $this->faker->numberBetween(0.00, 1.00),
            'communication_channel' => $this->faker->word,
            'status' => $this->faker->randomElement(['active', 'inactive', 'matched', 'completed']),
        ];
    }
}
