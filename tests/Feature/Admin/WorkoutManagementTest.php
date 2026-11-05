<?php
namespace Tests\Feature\Admin;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorkoutManagementTest extends TestCase {
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    private function payload(array $overrides = []): array {
        return array_merge([
            'title' => 'Upper Body Blast', 'description' => 'Push and pull',
            'category' => 'strength', 'difficulty' => 'medium',
            'estimated_duration' => 50, 'estimated_calories' => 350, 'is_public' => 'on',
        ], $overrides);
    }

    #[Test]
    public function admin_can_create_a_workout_with_exercises_in_order(): void {
        [$a, $b] = Exercise::factory()->count(2)->create();

        $this->actingAs($this->admin)
            ->post('/admin/workouts', $this->payload(['exercise_ids' => [$a->id, $b->id]]))
            ->assertRedirect(route('admin.workouts.index'));

        $workout = Workout::where('title', 'Upper Body Blast')->firstOrFail();
        $this->assertTrue($workout->is_public);
        $this->assertSame([$a->id, $b->id], $workout->exercises->pluck('id')->all());
        $this->assertEquals([1, 2], $workout->exercises->pluck('pivot.sort_order')->all());
    }

    #[Test]
    public function unchecked_public_box_creates_a_private_workout(): void {
        $payload = $this->payload();
        unset($payload['is_public']);
        $this->actingAs($this->admin)->post('/admin/workouts', $payload);

        $this->assertFalse(Workout::first()->is_public);
    }

    #[Test]
    public function invalid_workout_data_is_rejected(): void {
        $this->actingAs($this->admin)->post('/admin/workouts', $this->payload(['category' => 'yoga']))->assertSessionHasErrors('category');
        $this->actingAs($this->admin)->post('/admin/workouts', $this->payload(['difficulty' => 'extreme']))->assertSessionHasErrors('difficulty');
        $this->actingAs($this->admin)->post('/admin/workouts', $this->payload(['estimated_duration' => 0]))->assertSessionHasErrors('estimated_duration');
        $this->assertDatabaseCount('workouts', 0);
    }

    /** BUG-006 regression test: used to fail with a foreign-key SQL error (HTTP 500) */
    #[Test]
    public function an_unknown_exercise_id_is_a_validation_error_not_a_crash(): void {
        $this->actingAs($this->admin)
            ->post('/admin/workouts', $this->payload(['exercise_ids' => [99999]]))
            ->assertSessionHasErrors('exercise_ids.0');
        $this->assertDatabaseCount('workouts', 0);
    }

    /** BUG-001 regression test: editing a workout wiped sets/reps/rest of every exercise */
    #[Test]
    public function editing_a_workout_keeps_existing_sets_reps_and_order(): void {
        [$squat, $lunge, $plank] = Exercise::factory()->count(3)->create();
        $workout = Workout::factory()->create();
        $workout->exercises()->attach([
            $squat->id => ['sets' => 5, 'reps' => 5,  'rest_seconds' => 120, 'sort_order' => 1],
            $lunge->id => ['sets' => 3, 'reps' => 12, 'rest_seconds' => 60,  'sort_order' => 2],
        ]);

        // Same request the edit form sends: only the checked ids, in alphabetical order
        $this->actingAs($this->admin)
            ->put("/admin/workouts/{$workout->id}", $this->payload(['exercise_ids' => [$lunge->id, $plank->id, $squat->id]]))
            ->assertRedirect(route('admin.workouts.index'));

        $pivots = $workout->fresh()->exercises->keyBy('id');
        $this->assertEquals([5, 5, 120, 1],  [$pivots[$squat->id]->pivot->sets, $pivots[$squat->id]->pivot->reps, $pivots[$squat->id]->pivot->rest_seconds, $pivots[$squat->id]->pivot->sort_order]);
        $this->assertEquals([3, 12, 60, 2],  [$pivots[$lunge->id]->pivot->sets, $pivots[$lunge->id]->pivot->reps, $pivots[$lunge->id]->pivot->rest_seconds, $pivots[$lunge->id]->pivot->sort_order]);
        $this->assertEquals(3, $pivots[$plank->id]->pivot->sort_order);   // new exercise goes last
    }

    #[Test]
    public function unchecking_all_exercises_detaches_them(): void {
        $workout = Workout::factory()->create();
        $workout->exercises()->attach(Exercise::factory()->create(), ['sets' => 3, 'sort_order' => 1]);

        $this->actingAs($this->admin)->put("/admin/workouts/{$workout->id}", $this->payload());

        $this->assertCount(0, $workout->fresh()->exercises);
    }

    #[Test]
    public function deleting_a_workout_removes_its_exercise_links_and_logs(): void {
        $workout = Workout::factory()->create();
        $exercise = Exercise::factory()->create();
        $workout->exercises()->attach($exercise, ['sort_order' => 1]);
        $log = WorkoutLog::factory()->for($workout)->create();

        $this->actingAs($this->admin)->delete("/admin/workouts/{$workout->id}")->assertSessionHas('success');

        $this->assertModelMissing($workout);
        $this->assertModelMissing($log);
        $this->assertDatabaseMissing('workout_exercises', ['workout_id' => $workout->id]);
        $this->assertModelExists($exercise);
    }
}
