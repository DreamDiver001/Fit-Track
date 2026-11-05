<?php
namespace Tests\Unit;
use App\Models\Exercise;
use App\Models\Workout;
use App\Models\WorkoutLog;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ModelAccessorsTest extends TestCase {
    #[Test]
    public function tags_string_is_split_into_an_array(): void {
        $this->assertSame(['morning', 'gym'], (new WorkoutLog(['tags' => 'morning,gym']))->tags_array);
    }

    #[Test]
    public function empty_and_malformed_tags_give_a_clean_array(): void {
        $this->assertSame([], (new WorkoutLog(['tags' => '']))->tags_array);
        $this->assertSame([], (new WorkoutLog(['tags' => null]))->tags_array);
        $this->assertSame(['gym'], (new WorkoutLog(['tags' => ',gym,']))->tags_array);
    }

    #[Test]
    public function effort_label_has_a_fallback_for_unknown_values(): void {
        $this->assertSame('😓 Tough', (new WorkoutLog(['effort_rating' => 'tough']))->effort_label);
        $this->assertSame('Brutal', (new WorkoutLog(['effort_rating' => 'brutal']))->effort_label);
    }

    #[Test]
    public function workout_and_exercise_labels(): void {
        $workout = new Workout(['category' => 'hiit', 'difficulty' => 'hard']);
        $this->assertSame('HIIT', $workout->category_label);
        $this->assertSame('badge-error', $workout->difficulty_color);

        $exercise = new Exercise(['muscle_group' => 'full_body', 'equipment' => 'cable']);
        $this->assertSame('Full Body', $exercise->muscle_group_label);
        $this->assertSame('Cable', $exercise->equipment_label);
    }
}
