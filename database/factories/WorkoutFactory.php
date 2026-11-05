<?php
namespace Database\Factories;
use App\Models\Workout;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Workout> */
class WorkoutFactory extends Factory {
    public function definition(): array {
        return [
            'title'              => ucfirst(fake()->unique()->words(2, true)).' Workout',
            'description'        => fake()->sentence(),
            'category'           => fake()->randomElement(['cardio','strength','flexibility','hiit','core','full_body']),
            'difficulty'         => fake()->randomElement(['easy','medium','hard']),
            'estimated_duration' => fake()->numberBetween(15, 90),
            'estimated_calories' => fake()->numberBetween(100, 600),
            'is_public'          => true,
        ];
    }
    public function private(): static { return $this->state(['is_public' => false]); }
}
