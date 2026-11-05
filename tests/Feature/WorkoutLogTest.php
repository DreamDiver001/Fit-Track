<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorkoutLogTest extends TestCase {
    use RefreshDatabase;

    private User $user;
    private Workout $workout;

    protected function setUp(): void {
        parent::setUp();
        $this->user    = User::factory()->create();
        $this->workout = Workout::factory()->create();
    }

    private function validPayload(array $overrides = []): array {
        return array_merge([
            'workout_id'       => $this->workout->id,
            'logged_at'        => now()->toDateString(),
            'duration_minutes' => 45,
            'calories_burned'  => 300,
            'effort_rating'    => 'great',
            'notes'            => 'Felt strong',
            'tags'             => ['morning', 'gym'],
        ], $overrides);
    }

    #[Test]
    public function a_user_only_sees_their_own_logs(): void {
        WorkoutLog::factory()->for($this->user)->for($this->workout)->create(['notes' => 'mine']);
        $otherLog = WorkoutLog::factory()->create(['notes' => 'not mine']);

        $this->actingAs($this->user)->get('/logs')->assertOk()
            ->assertViewHas('logs', fn ($logs) => $logs->total() === 1 && !$logs->contains('id', $otherLog->id));
    }

    #[Test]
    public function the_create_form_renders(): void {
        $this->actingAs($this->user)->get('/logs/create')->assertOk()->assertSee($this->workout->title);
    }

    #[Test]
    public function a_user_can_log_a_workout_and_tags_are_stored(): void {
        $this->actingAs($this->user)->post('/logs', $this->validPayload())
            ->assertRedirect(route('logs.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('workout_logs', [
            'user_id' => $this->user->id, 'workout_id' => $this->workout->id,
            'duration_minutes' => 45, 'effort_rating' => 'great', 'tags' => 'morning,gym',
        ]);
    }

    #[Test]
    public function user_id_cannot_be_spoofed_through_the_form(): void {
        $victim = User::factory()->create();
        $this->actingAs($this->user)->post('/logs', $this->validPayload(['user_id' => $victim->id]));

        $this->assertSame(0, $victim->workoutLogs()->count());
        $this->assertSame(1, $this->user->workoutLogs()->count());
    }

    public static function invalidPayloads(): array {
        return [
            'missing workout'        => [['workout_id' => null], 'workout_id'],
            'unknown workout'        => [['workout_id' => 99999], 'workout_id'],
            'missing date'           => [['logged_at' => null], 'logged_at'],
            'impossible date'        => [['logged_at' => '2026-02-30'], 'logged_at'],
            'duration 0 (min 1)'     => [['duration_minutes' => 0], 'duration_minutes'],
            'duration 601 (max 600)' => [['duration_minutes' => 601], 'duration_minutes'],
            'negative calories'      => [['calories_burned' => -1], 'calories_burned'],
            'missing effort'         => [['effort_rating' => null], 'effort_rating'],
            'unknown effort'         => [['effort_rating' => 'brutal'], 'effort_rating'],
            'notes too long'         => [['notes' => str_repeat('a', 1001)], 'notes'],
            'tags not an array'      => [['tags' => 'morning'], 'tags'],
        ];
    }

    #[Test]
    #[DataProvider('invalidPayloads')]
    public function invalid_input_is_rejected(array $override, string $field): void {
        $this->actingAs($this->user)->post('/logs', $this->validPayload($override))
            ->assertSessionHasErrors($field);
        $this->assertDatabaseCount('workout_logs', 0);
    }

    #[Test]
    public function boundary_values_are_accepted(): void {
        $this->actingAs($this->user)->post('/logs', $this->validPayload(['duration_minutes' => 1]))->assertSessionHasNoErrors();
        $this->actingAs($this->user)->post('/logs', $this->validPayload(['duration_minutes' => 600]))->assertSessionHasNoErrors();
        $this->actingAs($this->user)->post('/logs', $this->validPayload(['calories_burned' => 0, 'tags' => []]))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('workout_logs', 3);
    }

    /** BUG-007 regression test */
    #[Test]
    public function a_workout_cannot_be_logged_in_the_future(): void {
        $this->actingAs($this->user)
            ->post('/logs', $this->validPayload(['logged_at' => now()->addDay()->toDateString()]))
            ->assertSessionHasErrors('logged_at');
    }

    /** BUG-004 regression test */
    #[Test]
    public function a_private_workout_cannot_be_logged(): void {
        $private = Workout::factory()->private()->create();
        $this->actingAs($this->user)
            ->post('/logs', $this->validPayload(['workout_id' => $private->id]))
            ->assertSessionHasErrors('workout_id');
    }

    #[Test]
    public function a_user_can_edit_and_update_their_log(): void {
        $log = WorkoutLog::factory()->for($this->user)->for($this->workout)->create();

        $this->actingAs($this->user)->get("/logs/{$log->id}/edit")->assertOk();
        $this->actingAs($this->user)
            ->put("/logs/{$log->id}", $this->validPayload(['effort_rating' => 'tough', 'tags' => []]))
            ->assertRedirect(route('logs.index'));

        $this->assertDatabaseHas('workout_logs', ['id' => $log->id, 'effort_rating' => 'tough', 'tags' => '']);
    }

    #[Test]
    public function a_user_can_delete_their_log(): void {
        $log = WorkoutLog::factory()->for($this->user)->for($this->workout)->create();
        $this->actingAs($this->user)->delete("/logs/{$log->id}")->assertRedirect(route('logs.index'));
        $this->assertDatabaseMissing('workout_logs', ['id' => $log->id]);
    }

    #[Test]
    public function a_user_cannot_view_edit_or_delete_someone_elses_log(): void {
        $log = WorkoutLog::factory()->create();   // belongs to another user

        $this->actingAs($this->user)->get("/logs/{$log->id}/edit")->assertForbidden();
        $this->actingAs($this->user)->put("/logs/{$log->id}", $this->validPayload())->assertForbidden();
        $this->actingAs($this->user)->delete("/logs/{$log->id}")->assertForbidden();
        $this->assertDatabaseHas('workout_logs', ['id' => $log->id]);
    }
}
