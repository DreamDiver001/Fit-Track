<?php
namespace Tests\Feature;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GoalTest extends TestCase {
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    private function payload(array $overrides = []): array {
        return array_merge([
            'title' => 'Lose 10 kg', 'goal_type' => 'weight_loss',
            'target_value' => 70, 'current_value' => 80, 'unit' => 'kg',
            'deadline' => now()->addMonths(3)->toDateString(), 'notes' => null,
        ], $overrides);
    }

    #[Test]
    public function a_user_can_create_a_goal_and_its_start_value_is_recorded(): void {
        $this->actingAs($this->user)->get('/goals/create')->assertOk();
        $this->actingAs($this->user)->post('/goals', $this->payload())->assertRedirect(route('goals.index'));

        $goal = Goal::first();
        $this->assertSame($this->user->id, $goal->user_id);
        $this->assertSame('in_progress', $goal->status);
        $this->assertEquals(80, $goal->start_value);
    }

    #[Test]
    public function goal_validation_rules_are_enforced(): void {
        $this->actingAs($this->user)->post('/goals', $this->payload(['title' => '']))->assertSessionHasErrors('title');
        $this->actingAs($this->user)->post('/goals', $this->payload(['goal_type' => 'cardio']))->assertSessionHasErrors('goal_type');
        $this->actingAs($this->user)->post('/goals', $this->payload(['target_value' => -1]))->assertSessionHasErrors('target_value');
        // boundary: deadline must be strictly after today
        $this->actingAs($this->user)->post('/goals', $this->payload(['deadline' => now()->toDateString()]))->assertSessionHasErrors('deadline');
        $this->assertDatabaseCount('goals', 0);
    }

    #[Test]
    public function the_goals_page_lists_only_my_goals_with_progress(): void {
        Goal::factory()->for($this->user)->create(['title' => 'My bench goal', 'target_value' => 100, 'current_value' => 50]);
        Goal::factory()->create(['title' => 'Someone else goal']);

        $this->actingAs($this->user)->get('/goals')->assertOk()
            ->assertSee('My bench goal')->assertSee('50%')->assertDontSee('Someone else goal');
    }

    #[Test]
    public function a_user_can_update_and_complete_a_goal(): void {
        $goal = Goal::factory()->for($this->user)->create();
        $this->actingAs($this->user)
            ->put("/goals/{$goal->id}", $this->payload(['goal_type' => 'strength', 'status' => 'completed']))
            ->assertRedirect(route('goals.index'));
        $this->assertSame('completed', $goal->fresh()->status);
    }

    /** BUG-008 regression test */
    #[Test]
    public function weight_loss_progress_works_when_the_goal_was_created_without_a_current_value(): void {
        $this->actingAs($this->user)->post('/goals', $this->payload(['current_value' => null]));
        $goal = Goal::first();

        $update = fn ($current) => $this->actingAs($this->user)
            ->put("/goals/{$goal->id}", $this->payload(['current_value' => $current, 'status' => 'in_progress']));

        $update(80);   // first weigh-in becomes the starting point
        $update(75);   // 5 of 10 kg lost

        $this->assertSame(50, $goal->fresh()->progress_percent);
    }

    #[Test]
    public function a_user_cannot_edit_or_delete_someone_elses_goal(): void {
        $goal = Goal::factory()->create();
        $this->actingAs($this->user)->get("/goals/{$goal->id}/edit")->assertForbidden();
        $this->actingAs($this->user)->put("/goals/{$goal->id}", $this->payload(['status' => 'completed']))->assertForbidden();
        $this->actingAs($this->user)->delete("/goals/{$goal->id}")->assertForbidden();
        $this->assertSame('in_progress', $goal->fresh()->status);
    }

    #[Test]
    public function a_user_can_delete_their_goal(): void {
        $goal = Goal::factory()->for($this->user)->create();
        $this->actingAs($this->user)->delete("/goals/{$goal->id}")->assertRedirect(route('goals.index'));
        $this->assertModelMissing($goal);
    }
}
