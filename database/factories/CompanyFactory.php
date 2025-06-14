<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Company>
 */
final class CompanyFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Company::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            // 'id' => Str::uuid(),
            'name' => $this->faker->company(), // fake()->company(),
            'logo_url' => $this->faker->url(),
            'website' => $this->faker->url(),
            'referral_program_url' => $this->faker->url(),
            'description' => $this->faker->paragraph(5, true),
            'industry' => $this->faker->word(),
        ];
    }
}
