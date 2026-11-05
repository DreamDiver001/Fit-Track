<?php
namespace Tests\Unit;
use App\Models\Goal;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/** Goal::progress_percent — equivalence classes and boundary values. */
class GoalProgressTest extends TestCase {
    private function goal(array $attrs): Goal {
        return new Goal($attrs + ['goal_type' => 'strength', 'status' => 'in_progress']);
    }

    public static function weightLossCases(): array {
        // [start, target, current, expected %]
        return [
            'no progress yet'          => [90, 70, 90, 0],
            'halfway'                  => [90, 70, 80, 50],
            'target reached exactly'   => [90, 70, 70, 100],
            'overshoot is capped'      => [90, 70, 65, 100],
            'weight gained is floored' => [90, 70, 95, 0],
            'rounding 1/3'             => [90, 60, 80, 33],
        ];
    }

    #[Test]
    #[DataProvider('weightLossCases')]
    public function weight_loss_progress_is_measured_from_the_start_value($start, $target, $current, $expected): void {
        $goal = $this->goal(['goal_type' => 'weight_loss', 'start_value' => $start, 'target_value' => $target, 'current_value' => $current]);
        $this->assertSame($expected, $goal->progress_percent);
    }

    #[Test]
    public function weight_loss_with_start_below_target_is_zero_not_negative(): void {
        $goal = $this->goal(['goal_type' => 'weight_loss', 'start_value' => 60, 'target_value' => 70, 'current_value' => 65]);
        $this->assertSame(0, $goal->progress_percent);
    }

    public static function increasingGoalCases(): array {
        return [
            'zero'          => [100, 0, 0],
            'quarter'       => [100, 25, 25],
            'complete'      => [100, 100, 100],
            'over target'   => [100, 150, 100],
            'decimal input' => [42.5, 21.25, 50],
        ];
    }

    #[Test]
    #[DataProvider('increasingGoalCases')]
    public function strength_progress_is_current_divided_by_target($target, $current, $expected): void {
        $goal = $this->goal(['target_value' => $target, 'current_value' => $current]);
        $this->assertSame($expected, $goal->progress_percent);
    }

    #[Test]
    public function goal_without_target_has_zero_progress(): void {
        $this->assertSame(0, $this->goal(['target_value' => null, 'current_value' => 50])->progress_percent);
    }

    #[Test]
    public function labels_and_status_colours_map_correctly(): void {
        $goal = $this->goal(['goal_type' => 'weight_loss', 'status' => 'completed']);
        $this->assertSame('Weight Loss', $goal->goal_type_label);
        $this->assertSame('badge-success', $goal->status_color);
    }
}
