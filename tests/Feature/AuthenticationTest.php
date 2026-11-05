<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticationTest extends TestCase {
    use RefreshDatabase;

    #[Test]
    public function login_and_register_pages_render(): void {
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    #[Test]
    public function a_visitor_can_register_and_is_logged_in(): void {
        $this->post('/register', [
            'name' => 'Test User', 'email' => 'test@example.com',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'is_admin' => false]);
    }

    #[Test]
    public function registration_rejects_invalid_input(): void {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->post('/register', ['name' => 'A', 'email' => 'taken@example.com', 'password' => 'password123', 'password_confirmation' => 'password123'])
            ->assertSessionHasErrors('email');
        $this->post('/register', ['name' => 'A', 'email' => 'a@example.com', 'password' => 'short7!', 'password_confirmation' => 'short7!'])
            ->assertSessionHasErrors('password');   // boundary: 7 chars < min 8
        $this->post('/register', ['name' => 'A', 'email' => 'b@example.com', 'password' => 'password123', 'password_confirmation' => 'different123'])
            ->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    #[Test]
    public function a_user_can_log_in_with_correct_credentials(): void {
        $user = User::factory()->create();
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function wrong_password_is_rejected(): void {
        $user = User::factory()->create();
        $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function a_banned_user_cannot_log_in(): void {
        $user = User::factory()->banned()->create();
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function a_user_can_log_out(): void {
        $this->actingAs(User::factory()->create())->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    #[Test]
    public function guests_are_redirected_from_protected_pages(): void {
        foreach (['/dashboard', '/logs', '/logs/create', '/goals', '/goals/create'] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    /** BUG-005 regression test */
    #[Test]
    public function a_user_banned_during_their_session_is_logged_out_on_next_request(): void {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/dashboard')->assertOk();

        $user->update(['is_banned' => true]);

        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
