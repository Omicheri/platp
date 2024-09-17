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
    protected $model = Plat::class;

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
            'user_id'=> User::inRandomOrder()->pluck('id')->first(),
            'image' => $this->faker->imageUrl($width = 320, $height = 240 , 'dish'),
        ];
    }
}
