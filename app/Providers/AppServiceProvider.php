<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\WorkoutLog;
use App\Models\Goal;
use App\Policies\WorkoutLogPolicy;
use App\Policies\GoalPolicy;

class AppServiceProvider extends ServiceProvider {
    public function register(): void {}
    public function boot(): void {
        Gate::policy(WorkoutLog::class, WorkoutLogPolicy::class);
        Gate::policy(Goal::class, GoalPolicy::class);
    }
}
