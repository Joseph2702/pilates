<?php

namespace Database\Seeders;

use App\Domain\Entity\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Comment out factory untuk sekarang, karena User bisa dibuat via API
        // User::factory(10)->create();

        // Uncomment jika ingin membuat test user dengan factory:
        // User::factory()->create([
        //     'nama' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Jalankan AdminSeeder untuk membuat admin user
        $this->call(AdminSeeder::class);
    }
}
