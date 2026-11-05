<?php
namespace Database\Factories;
use App\Models\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Exercise> */
class ExerciseFactory extends Factory {
    public function definition(): array {
        return [
            'name'         => ucfirst(fake()->unique()->words(2, true)),
            'description'  => fake()->sentence(),
            'muscle_group' => fake()->randomElement(['chest','back','legs','core','arms','shoulders','full_body']),
            'equipment'    => fake()->randomElement(['barbell','dumbbell','bodyweight','machine','cable','other']),
            'instructions' => fake()->paragraph(),
        ];
    }
}
