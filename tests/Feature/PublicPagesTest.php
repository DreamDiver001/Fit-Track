<?php
namespace Tests\Feature;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicPagesTest extends TestCase {
    use RefreshDatabase;

    #[Test]
    public function home_page_shows_only_public_workouts(): void {
        $public  = Workout::factory()->create(['title' => 'Public Burner']);
        $private = Workout::factory()->private()->create(['title' => 'Secret Plan']);

        $this->get('/')->assertOk()->assertSee('Public Burner')->assertDontSee('Secret Plan');
    }

    #[Test]
    public function workouts_list_filters_by_category_difficulty_and_search(): void {
        Workout::factory()->create(['title' => 'Morning Run', 'category' => 'cardio', 'difficulty' => 'easy']);
        Workout::factory()->create(['title' => 'Heavy Squats', 'category' => 'strength', 'difficulty' => 'hard']);

        $this->get('/workouts?category=cardio')->assertOk()->assertSee('Morning Run')->assertDontSee('Heavy Squats');
        $this->get('/workouts?difficulty=hard')->assertSee('Heavy Squats')->assertDontSee('Morning Run');
        $this->get('/workouts?search=squat')->assertSee('Heavy Squats')->assertDontSee('Morning Run');
    }

    #[Test]
    public function workouts_list_is_paginated_at_nine(): void {
        Workout::factory()->count(10)->create();
        $this->get('/workouts')->assertViewHas('workouts', fn ($p) => $p->count() === 9 && $p->total() === 10);
    }

    #[Test]
    public function a_public_workout_detail_page_shows_its_exercises(): void {
        $workout  = Workout::factory()->create();
        $exercise = Exercise::factory()->create(['name' => 'Kettlebell Swing']);
        $workout->exercises()->attach($exercise, ['sets' => 3, 'reps' => 12, 'rest_seconds' => 45, 'sort_order' => 1]);

        $this->get("/workouts/{$workout->id}")->assertOk()->assertSee('Kettlebell Swing');
    }

    /** BUG-003 regression test */
    #[Test]
    public function a_private_workout_is_not_visible_by_direct_url(): void {
        $private = Workout::factory()->private()->create();

        $this->get("/workouts/{$private->id}")->assertNotFound();
        $this->actingAs(User::factory()->create())->get("/workouts/{$private->id}")->assertNotFound();
    }

    #[Test]
    public function an_admin_can_preview_a_private_workout(): void {
        $private = Workout::factory()->private()->create();
        $this->actingAs(User::factory()->admin()->create())->get("/workouts/{$private->id}")->assertOk();
    }

    #[Test]
    public function unknown_workout_returns_404(): void {
        $this->get('/workouts/99999')->assertNotFound();
    }

    #[Test]
    public function exercise_library_filters_by_muscle_group_and_equipment(): void {
        Exercise::factory()->create(['name' => 'Bench Press', 'muscle_group' => 'chest', 'equipment' => 'barbell']);
        Exercise::factory()->create(['name' => 'Plank Hold', 'muscle_group' => 'core', 'equipment' => 'bodyweight']);

        $this->get('/exercises?muscle_group=chest')->assertOk()->assertSee('Bench Press')->assertDontSee('Plank Hold');
        $this->get('/exercises?equipment=bodyweight')->assertSee('Plank Hold')->assertDontSee('Bench Press');
    }
}
