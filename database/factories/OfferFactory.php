<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use \App\Models\User;
use \App\Models\Company;


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
            'offerer_id' => User::factory(),
            'company_id' => Company::factory(),
            'offerer_type' => $this->faker->randomElement(['referrer', 'referred']),
            'title' => $this->faker->sentence(6, true),
            'description' => $this->faker->optional($weight = 0.7)->paragraph(4, true),
            'reward_total_cents' => $this->faker->numberBetween(1000, 10000),
            'reward_offerer_percent' => $this->faker->randomFloat(2, 0, 1),
            'status' => $this->faker->randomElement(['draft', 'live', 'hidden', 'matched', 'deleted']),
            'admin_status' => $this->faker->randomElement(['active', 'inactive', 'review', 'archived']),
        ];
    }
}
