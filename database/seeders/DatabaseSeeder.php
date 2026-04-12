<?php

namespace Database\Seeders;

use App\Models\User;
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
        // Create admin user with @portal.com email
        User::factory()->admin()->create([
            'name' => 'Admin Portal',
            'email' => 'admin@portal.com',
            'status' => 'active',
        ]);

        // Create regular users with @gmail.com emails
        User::factory(10)->gmailUser()->create([
            'status' => 'active',
        ]);

        // Seed job postings
        $this->call(JobPostingSeeder::class);

        // Seed applicants
        $this->call(ApplicantSeeder::class);

        // Seed notifications
        $this->call(NotificationSeeder::class);
    }
}
