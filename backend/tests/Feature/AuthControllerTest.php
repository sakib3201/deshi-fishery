<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PassportClientSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private const string TEST_EMAIL = 'test@example.com';

    private const string TEST_PASSWORD = 'password123';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PassportClientSeeder::class);
    }

    public function createUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'email' => self::TEST_EMAIL,
            'password' => bcrypt(self::TEST_PASSWORD),
        ], $overrides));
    }

    private function loginAndGetToken(): string
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD,
        ]);

        return $response->json('data.access_token');
    }

    private function assertAuthSuccessResponse(TestResponse $response, int $status = 200): TestResponse
    {
        return $response->assertStatus($status)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'access_token',
                    'refresh_token',
                    'expires_in',
                ],
            ])
            ->assertJsonPath('success', true);
    }

    public function test_successful_registration_returns_201_with_tokens(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD,
            'password_confirmation' => self::TEST_PASSWORD,
        ]);

        $this->assertAuthSuccessResponse($response, 201)
            ->assertJsonPath('data.user.role', 'owner');

        $this->assertDatabaseHas('users', [
            'email' => self::TEST_EMAIL,
            'name' => 'Test User',
            'role' => 'owner',
        ]);
    }

    public function test_registration_with_duplicate_email_returns_422(): void
    {
        $this->createUser();

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD,
            'password_confirmation' => self::TEST_PASSWORD,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_with_invalid_password_returns_422(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => self::TEST_EMAIL,
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_with_mismatched_password_confirmation_returns_422(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD,
            'password_confirmation' => 'different_password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_successful_login_returns_200_with_tokens(): void
    {
        $this->createUser();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD,
        ]);

        $this->assertAuthSuccessResponse($response)
            ->assertJsonPath('data.user.email', self::TEST_EMAIL);
    }

    public function test_login_with_invalid_password_returns_401(): void
    {
        $this->createUser();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => self::TEST_EMAIL,
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'InvalidCredentials');
    }

    public function test_login_with_nonexistent_email_returns_401(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => self::TEST_PASSWORD,
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'InvalidCredentials');
    }

    public function test_successful_logout_revokes_token(): void
    {
        $this->createUser();
        $accessToken = $this->loginAndGetToken();

        $response = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(204);
    }

    public function test_logout_with_invalid_token_returns_401(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid_token')
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);
    }

    public function test_successful_token_refresh_returns_new_access_token(): void
    {
        $this->createUser();
        $accessToken = $this->loginAndGetToken();

        $response = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->postJson('/api/v1/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'access_token',
                    'refresh_token',
                    'expires_in',
                ],
            ])
            ->assertJsonPath('success', true);

        $newAccessToken = $response->json('data.access_token');
        $this->assertNotEmpty($newAccessToken);
        $this->assertNotEquals($accessToken, $newAccessToken);
    }

    public function test_token_refresh_with_invalid_token_returns_401(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid_token')
            ->postJson('/api/v1/auth/refresh');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_retrieve_profile(): void
    {
        $user = $this->createUser();
        $accessToken = $this->loginAndGetToken();

        $response = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', self::TEST_EMAIL);
    }

    public function test_unauthenticated_user_cannot_retrieve_profile(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }

    public function test_password_is_hashed_with_bcrypt(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD,
            'password_confirmation' => self::TEST_PASSWORD,
        ]);

        $user = User::where('email', self::TEST_EMAIL)->first();
        $this->assertTrue(password_verify(self::TEST_PASSWORD, $user->password));
        $this->assertStringStartsWith('$2y$', $user->password);
    }
}
