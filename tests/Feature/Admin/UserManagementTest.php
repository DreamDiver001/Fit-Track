<?php
namespace Tests\Feature\Admin;
use App\Models\Goal;
use App\Models\User;
use App\Models\WorkoutLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserManagementTest extends TestCase {
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    #[Test]
    public function admin_can_search_users_by_name_or_email(): void {
        User::factory()->create(['name' => 'Alice Runner', 'email' => 'alice@example.com']);
        User::factory()->create(['name' => 'Bob Lifter',   'email' => 'bob@example.com']);

        $this->actingAs($this->admin)->get('/admin/users?search=alice')->assertOk()
            ->assertSee('Alice Runner')->assertDontSee('Bob Lifter');
        $this->actingAs($this->admin)->get('/admin/users?search=bob@')
            ->assertSee('Bob Lifter')->assertDontSee('Alice Runner');
    }

    #[Test]
    public function admin_can_ban_and_unban_a_user(): void {
        $user = User::factory()->create();

        $this->actingAs($this->admin)->post("/admin/users/{$user->id}/toggle-ban")->assertSessionHas('success', 'User banned.');
        $this->assertTrue($user->fresh()->is_banned);

        $this->actingAs($this->admin)->post("/admin/users/{$user->id}/toggle-ban")->assertSessionHas('success', 'User unbanned.');
        $this->assertFalse($user->fresh()->is_banned);
    }

    #[Test]
    public function admins_cannot_be_banned_or_deleted(): void {
        $otherAdmin = User::factory()->admin()->create();

        $this->actingAs($this->admin)->post("/admin/users/{$otherAdmin->id}/toggle-ban")->assertSessionHas('error');
        $this->actingAs($this->admin)->delete("/admin/users/{$otherAdmin->id}")->assertSessionHas('error');

        $this->assertFalse($otherAdmin->fresh()->is_banned);
        $this->assertModelExists($otherAdmin);
    }

    #[Test]
    public function deleting_a_user_also_deletes_their_logs_and_goals(): void {
        $user = User::factory()->create();
        $log  = WorkoutLog::factory()->for($user)->create();
        $goal = Goal::factory()->for($user)->create();

        $this->actingAs($this->admin)->delete("/admin/users/{$user->id}")->assertSessionHas('success');

        $this->assertModelMissing($user);
        $this->assertModelMissing($log);
        $this->assertModelMissing($goal);
    }
}
