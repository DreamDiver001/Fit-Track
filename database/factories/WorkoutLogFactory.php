<?php
namespace Database\Factories;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WorkoutLog> */
class WorkoutLogFactory extends Factory {
    public function definition(): array {
        return [
            'user_id'          => User::factory(),
            'workout_id'       => Workout::factory(),
            'logged_at'        => fake()->dateTimeBetween('-30 days', '-1 day')->format('Y-m-d'),
            'duration_minutes' => fake()->numberBetween(10, 120),
            'calories_burned'  => fake()->numberBetween(50, 800),
            'effort_rating'    => fake()->randomElement(['great','okay','tough']),
            'notes'            => fake()->sentence(),
            'tags'             => '',
        ];
    }
}
