<?php
namespace Tests\Unit;
use App\Models\Goal;
use App\Models\User;
use App\Models\WorkoutLog;
use App\Policies\GoalPolicy;
use App\Policies\WorkoutLogPolicy;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PolicyTest extends TestCase {
    private function user(int $id): User {
        return (new User())->forceFill(['id' => $id]);
    }

    #[Test]
    public function owner_can_update_and_delete_their_log(): void {
        $log = (new WorkoutLog())->forceFill(['user_id' => 1]);
        $this->assertTrue((new WorkoutLogPolicy)->update($this->user(1), $log));
        $this->assertTrue((new WorkoutLogPolicy)->delete($this->user(1), $log));
    }

    #[Test]
    public function other_users_cannot_touch_a_log(): void {
        $log = (new WorkoutLog())->forceFill(['user_id' => 1]);
        $this->assertFalse((new WorkoutLogPolicy)->update($this->user(2), $log));
        $this->assertFalse((new WorkoutLogPolicy)->delete($this->user(2), $log));
    }

    #[Test]
    public function goal_policy_only_allows_the_owner(): void {
        $goal = (new Goal())->forceFill(['user_id' => 5]);
        $this->assertTrue((new GoalPolicy)->update($this->user(5), $goal));
        $this->assertFalse((new GoalPolicy)->delete($this->user(6), $goal));
    }
}
