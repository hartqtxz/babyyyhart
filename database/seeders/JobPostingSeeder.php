<?php

namespace Database\Seeders;

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobPostingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();

        JobPosting::create([
            'user_id' => $admin->id,
            'title' => 'Construction Worker',
            'description' => 'We are looking for experienced construction workers for a residential project.',
            'workers_needed' => 3,
            'salary_min' => 25000,
            'salary_max' => 35000,
            'location' => 'New York, NY',
            'job_type' => 'Full-time',
            'experience_level' => 'Mid Level',
            'status' => 'active',
        ]);

        JobPosting::create([
            'user_id' => $admin->id,
            'title' => 'Programmer',
            'description' => 'Seeking full-stack developers with React and Node.js experience.',
            'workers_needed' => 5,
            'salary_min' => 60000,
            'salary_max' => 90000,
            'location' => 'San Francisco, CA',
            'job_type' => 'Full-time',
            'experience_level' => 'Senior',
            'status' => 'active',
        ]);

        JobPosting::create([
            'user_id' => $admin->id,
            'title' => 'Sales Manager',
            'description' => 'Experienced sales manager needed for team leadership.',
            'workers_needed' => 2,
            'salary_min' => 45000,
            'salary_max' => 65000,
            'location' => 'Chicago, IL',
            'job_type' => 'Full-time',
            'experience_level' => 'Senior',
            'status' => 'active',
        ]);
    }
}
