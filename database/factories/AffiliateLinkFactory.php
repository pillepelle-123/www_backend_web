<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AffiliateLink>
 */
class AffiliateLinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'url' => $this->faker->url(),
            'parameters' => $this->faker->optional()->word(),
            'click_count' => $this->faker->numberBetween(0, 1000),
            'admin_status' => $this->faker->randomElement(['pending', 'active', 'inactive', 'review', 'archived']),
        ];
    }
    
    /**
     * Indicate that the affiliate link is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_status' => 'active',
        ]);
    }
    
    /**
     * Indicate that the affiliate link is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_status' => 'pending',
        ]);
    }
}