<?php
namespace Tests\Feature\Admin;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExerciseManagementTest extends TestCase {
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        Storage::fake('public');
    }

    private function payload(array $overrides = []): array {
        return array_merge([
            'name' => 'Romanian Deadlift', 'description' => 'Hip hinge',
            'muscle_group' => 'legs', 'equipment' => 'barbell', 'instructions' => 'Hinge at the hips.',
        ], $overrides);
    }

    private function jpeg(string $name = 'photo.jpg', int $kb = 100): UploadedFile {
        return UploadedFile::fake()->create($name, $kb, 'image/jpeg');
    }

    #[Test]
    public function admin_can_create_an_exercise_with_an_image(): void {
        $this->actingAs($this->admin)->post('/admin/exercises', $this->payload(['image' => $this->jpeg()]))
            ->assertRedirect(route('admin.exercises.index'));

        $exercise = Exercise::where('name', 'Romanian Deadlift')->firstOrFail();
        $this->assertStringStartsWith('exercises/', $exercise->image_path);
        Storage::disk('public')->assertExists($exercise->image_path);
    }

    #[Test]
    public function image_is_optional(): void {
        $this->actingAs($this->admin)->post('/admin/exercises', $this->payload())->assertSessionHasNoErrors();
        $this->assertNull(Exercise::first()->image_path);
    }

    #[Test]
    public function invalid_exercise_data_is_rejected(): void {
        $this->actingAs($this->admin)->post('/admin/exercises', $this->payload(['name' => '']))->assertSessionHasErrors('name');
        $this->actingAs($this->admin)->post('/admin/exercises', $this->payload(['muscle_group' => 'neck']))->assertSessionHasErrors('muscle_group');
        $this->actingAs($this->admin)->post('/admin/exercises', $this->payload(['equipment' => 'kettlebell']))->assertSessionHasErrors('equipment');
        $this->assertDatabaseCount('exercises', 0);
    }

    #[Test]
    public function non_image_and_oversized_uploads_are_rejected(): void {
        $pdf = UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf');
        $this->actingAs($this->admin)->post('/admin/exercises', $this->payload(['image' => $pdf]))->assertSessionHasErrors('image');

        // boundary: max is 2048 KB
        $this->actingAs($this->admin)->post('/admin/exercises', $this->payload(['image' => $this->jpeg('big.jpg', 2049)]))->assertSessionHasErrors('image');
        $this->actingAs($this->admin)->post('/admin/exercises', $this->payload(['image' => $this->jpeg('ok.jpg', 2048)]))->assertSessionHasNoErrors();
    }

    #[Test]
    public function replacing_an_image_deletes_the_old_file(): void {
        Storage::disk('public')->put('exercises/old.jpg', 'old');
        $exercise = Exercise::factory()->create(['image_path' => 'exercises/old.jpg']);

        $this->actingAs($this->admin)
            ->put("/admin/exercises/{$exercise->id}", $this->payload(['name' => 'Renamed', 'image' => $this->jpeg('new.jpg')]))
            ->assertRedirect(route('admin.exercises.index'));

        $exercise->refresh();
        $this->assertSame('Renamed', $exercise->name);
        Storage::disk('public')->assertMissing('exercises/old.jpg');
        Storage::disk('public')->assertExists($exercise->image_path);
    }

    #[Test]
    public function deleting_an_exercise_removes_its_image_and_workout_links(): void {
        Storage::disk('public')->put('exercises/x.jpg', 'x');
        $exercise = Exercise::factory()->create(['image_path' => 'exercises/x.jpg']);
        $workout  = Workout::factory()->create();
        $workout->exercises()->attach($exercise, ['sets' => 3, 'sort_order' => 1]);

        $this->actingAs($this->admin)->delete("/admin/exercises/{$exercise->id}")->assertSessionHas('success');

        $this->assertModelMissing($exercise);
        Storage::disk('public')->assertMissing('exercises/x.jpg');
        $this->assertDatabaseMissing('workout_exercises', ['exercise_id' => $exercise->id]);
        $this->assertModelExists($workout);
    }
}
