<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Rating;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Rating>
 */
final class RatingFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Rating::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            // 'id' => Str::uuid(),
            'offer_id' => \App\Models\Offer::factory(),
            'from_user_id' => \App\Models\User::factory(),
            'to_user_id' => \App\Models\User::factory(),
            'score' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'comment' => $this->faker->optional($weight = 0.7)->paragraph(3, true),
        ];
    }
}
