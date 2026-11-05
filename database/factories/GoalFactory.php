<?php
namespace Database\Factories;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Goal> */
class GoalFactory extends Factory {
    public function definition(): array {
        return [
            'user_id'       => User::factory(),
            'title'         => fake()->sentence(3),
            'goal_type'     => 'strength',
            'start_value'   => 50,
            'target_value'  => 100,
            'current_value' => 50,
            'unit'          => 'kg',
            'deadline'      => now()->addMonth()->format('Y-m-d'),
            'status'        => 'in_progress',
            'notes'         => null,
        ];
    }
}
