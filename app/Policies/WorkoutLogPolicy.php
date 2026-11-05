<?php
namespace App\Policies;
use App\Models\User;
use App\Models\WorkoutLog;

class WorkoutLogPolicy {
    public function update(User $user, WorkoutLog $log): bool { return $user->id === $log->user_id; }
    public function delete(User $user, WorkoutLog $log): bool { return $user->id === $log->user_id; }
}
