<?php

namespace Database\Factories;

use App\Models\Plat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plat>
 */

class PlatFactory extends Factory
{


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titre' => $this->faker->Name(),
            'recette' => $this->faker->paragraphs(rand(2, 5), true),
            'likes' => $this->faker->numberBetween(1, 100),
            'image' => $this->faker->imageUrl($width = 320, $height = 240, 'dish'),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Plat $plat) {
            $plat->user()->existsOr(
                function () use ($plat) {
                    $plat->user()->associate(User::factory()->createOne());
                }
            );
        });
    }
}
