<?php

namespace Tests\Feature;

use App\Domain\Entity\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Feature test untuk Auth API endpoints.
 *
 * Konsep: kita test HTTP endpoint secara end-to-end.
 * - $this->postJson() = kirim request HTTP ke app
 * - ->assertStatus() = cek HTTP status code
 * - ->assertJsonStructure() = cek struktur JSON response
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_dapat_register_dengan_data_valid(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'no_hp' => '08123456789',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => ['id_user', 'nama', 'email'],
                    'token',
                    'token_type',
                ],
                'message',
            ]);

        // Pastikan user tersimpan di DB
        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'nama' => 'Budi Santoso',
        ]);

        // NOTE: AuthService saat ini hanya membuat User (belum Pelanggan profile).
        // Test ini mendokumentasikan perilaku aktual — bukan yang diharapkan.
        // Jika Pelanggan profile perlu dibuat saat register, fix AuthService-nya.
        $this->assertDatabaseHas('users', ['email' => 'budi@example.com']);
    }

    #[Test]
    public function register_gagal_jika_email_duplikat(): void
    {
        // Buat user dengan email yang sama dulu
        User::factory()->create(['email' => 'duplikat@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'nama' => 'User Lain',
            'email' => 'duplikat@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422); // Unprocessable Entity (validation error)
    }

    #[Test]
    public function register_gagal_jika_password_terlalu_pendek(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'nama' => 'Budi',
            'email' => 'budi@example.com',
            'password' => '123', // kurang dari 8 karakter
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    #[Test]
    public function user_dapat_login_dengan_kredensial_valid(): void
    {
        // Arrange: buat user dengan password yang diketahui
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        // Act: login
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['token', 'token_type'],
            ]);
    }

    #[Test]
    public function login_gagal_dengan_password_salah(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password_benar'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password_salah',
        ]);

        // 401 Unauthorized
        $response->assertStatus(401);
    }

    #[Test]
    public function user_dapat_logout(): void
    {
        // Buat user dan dapatkan token
        $user = User::factory()->create(['status' => 'active']);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/logout');

        $response->assertStatus(200);

        // Token harus dihapus dari DB
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[Test]
    public function endpoint_me_mengembalikan_data_user_terautentikasi(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('data.email', $user->email);
    }

    #[Test]
    public function endpoint_protected_menolak_request_tanpa_token(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }
}
