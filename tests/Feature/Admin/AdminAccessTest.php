<?php
namespace Tests\Feature\Admin;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminAccessTest extends TestCase {
    use RefreshDatabase;

    private const ADMIN_PAGES = [
        '/admin', '/admin/users', '/admin/workouts', '/admin/workouts/create',
        '/admin/exercises', '/admin/exercises/create',
    ];

    #[Test]
    public function guests_are_sent_to_login(): void {
        foreach (self::ADMIN_PAGES as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    #[Test]
    public function regular_users_get_403_on_every_admin_page(): void {
        $user = User::factory()->create();
        foreach (self::ADMIN_PAGES as $url) {
            $this->actingAs($user)->get($url)->assertForbidden();
        }
    }

    #[Test]
    public function regular_users_cannot_perform_admin_actions(): void {
        $user   = User::factory()->create();
        $victim = User::factory()->create();
        $this->actingAs($user)->post("/admin/users/{$victim->id}/toggle-ban")->assertForbidden();
        $this->actingAs($user)->delete("/admin/users/{$victim->id}")->assertForbidden();
        $this->assertFalse($victim->fresh()->is_banned);
    }

    #[Test]
    public function admins_can_open_every_admin_page(): void {
        $admin = User::factory()->admin()->create();
        WorkoutLog::factory()->count(2)->create();   // gives the dashboard some data
        $workout  = Workout::factory()->create();
        $exercise = Exercise::factory()->create();

        foreach ([...self::ADMIN_PAGES, "/admin/workouts/{$workout->id}/edit", "/admin/exercises/{$exercise->id}/edit"] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    #[Test]
    public function admin_dashboard_counts_are_correct(): void {
        $admin = User::factory()->admin()->create();
        WorkoutLog::factory()->count(3)->create();   // also creates 3 users + 3 workouts

        $this->actingAs($admin)->get('/admin')->assertViewHas('stats', fn ($s) =>
            $s['users'] === 4 && $s['workouts'] === 3 && $s['logs'] === 3);
    }

    /** BUG-002 regression test: resource "show" routes had no controller method (500 error) */
    #[Test]
    public function admin_resource_urls_without_a_page_do_not_crash(): void {
        $admin = User::factory()->admin()->create();
        $workout  = Workout::factory()->create();
        $exercise = Exercise::factory()->create();

        $this->actingAs($admin)->get("/admin/workouts/{$workout->id}")->assertStatus(405);
        $this->actingAs($admin)->get("/admin/exercises/{$exercise->id}")->assertStatus(405);
    }
}
