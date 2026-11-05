<?php
namespace Tests\Feature;
use App\Models\Goal;
use App\Models\User;
use App\Models\WorkoutLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DashboardTest extends TestCase {
    use RefreshDatabase;

    private function logOn(User $user, int $daysAgo, int $minutes = 30): void {
        WorkoutLog::factory()->for($user)->create([
            'logged_at' => now()->subDays($daysAgo)->toDateString(),
            'duration_minutes' => $minutes,
        ]);
    }

    #[Test]
    public function dashboard_shows_totals_for_the_current_user_only(): void {
        $user = User::factory()->create();
        $this->logOn($user, 1, 30);
        $this->logOn($user, 3, 45);
        Goal::factory()->for($user)->create();
        WorkoutLog::factory()->create(['duration_minutes' => 999]);   // other user

        $this->actingAs($user)->get('/dashboard')->assertOk()
            ->assertViewHas('totalLogs', 2)
            ->assertViewHas('totalMinutes', 75)
            ->assertViewHas('totalGoals', 1);
    }

    #[Test]
    public function streak_counts_consecutive_days_ending_today(): void {
        $user = User::factory()->create();
        foreach ([0, 1, 2, 4] as $d) $this->logOn($user, $d);   // gap on day 3

        $this->actingAs($user)->get('/dashboard')->assertViewHas('streak', 3);
    }

    #[Test]
    public function two_logs_on_the_same_day_count_once_in_the_streak(): void {
        $user = User::factory()->create();
        $this->logOn($user, 0);
        $this->logOn($user, 0);

        $this->actingAs($user)->get('/dashboard')->assertViewHas('streak', 1);
    }

    #[Test]
    public function streak_is_zero_without_a_log_today(): void {
        $user = User::factory()->create();
        $this->logOn($user, 1);

        $this->actingAs($user)->get('/dashboard')->assertViewHas('streak', 0);
    }
}
