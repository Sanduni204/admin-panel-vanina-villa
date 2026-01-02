<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    /**
     * Test: User can view the forgot password form
     */
    public function test_user_can_view_forgot_password_form(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertViewIs('auth.forgot-password');
    }

    /**
     * Test: User can request a password reset link
     */
    public function test_user_can_request_password_reset_link(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    /**
     * Test: Non-existing user doesn't get password reset notification
     */
    public function test_non_existing_user_doesnt_get_reset_notification(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertRedirect();
        Notification::assertNothingSent();
    }

    /**
     * Test: Forgot password requires email validation
     */
    public function test_forgot_password_requires_valid_email(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: Forgot password requires email field
     */
    public function test_forgot_password_requires_email(): void
    {
        $response = $this->post('/forgot-password', []);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: User can view reset password form with valid token
     */
    public function test_user_can_view_reset_password_form(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->get("/reset-password/{$token}?email={$user->email}");

        $response->assertStatus(200);
        $response->assertViewIs('auth.reset-password');
        $response->assertViewHas('token', $token);
        $response->assertViewHas('email', $user->email);
    }

    /**
     * Test: User can reset password with valid token and credentials
     */
    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('status');

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('NewPassword123!', $user->fresh()->password));
    }

    /**
     * Test: Reset password validation - password must have 8+ characters
     */
    public function test_reset_password_requires_min_8_characters(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Pass1!',
            'password_confirmation' => 'Pass1!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: Reset password validation - password must contain mixed case
     */
    public function test_reset_password_requires_mixed_case(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'password123!',
            'password_confirmation' => 'password123!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: Reset password validation - password must contain numbers
     */
    public function test_reset_password_requires_numbers(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Password!',
            'password_confirmation' => 'Password!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: Reset password validation - password must contain symbols
     */
    public function test_reset_password_requires_symbols(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: Reset password validation - passwords must match
     */
    public function test_reset_password_requires_confirmation_match(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: Reset password validation - token is required
     */
    public function test_reset_password_requires_token(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/reset-password', [
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('token');
    }

    /**
     * Test: Reset password validation - email is required
     */
    public function test_reset_password_requires_email(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: Reset password validation - email must be valid format
     */
    public function test_reset_password_requires_valid_email_format(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'invalid-email',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: Reset password with invalid token fails
     */
    public function test_reset_password_with_invalid_token_fails(): void
    {
        $user = User::factory()->create();
        $invalidToken = 'invalid-token-hash';

        $response = $this->post('/reset-password', [
            'token' => $invalidToken,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: Reset password with wrong email fails
     */
    public function test_reset_password_with_wrong_email_fails(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $otherUser->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
        // Original user's password should not change
        $this->assertFalse(\Illuminate\Support\Facades\Hash::check('NewPassword123!', $user->fresh()->password));
    }

    /**
     * Test: Old password doesn't work after reset
     */
    public function test_old_password_doesnt_work_after_reset(): void
    {
        $user = User::factory()->create([
            'password' => \Illuminate\Support\Facades\Hash::make('OldPassword123!'),
        ]);
        $token = Password::createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'OldPassword123!',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /**
     * Test: User can login with new password after reset
     */
    public function test_user_can_login_with_new_password_after_reset(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'NewPassword123!',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
    }
}
