<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\User;
use App\Models\JobPosting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('id', '!=', 1)->limit(5)->get();
        $jobs = JobPosting::all();

        foreach ($users as $index => $user) {
            Applicant::create([
                'user_id' => $user->id,
                'job_posting_id' => $jobs[$index % count($jobs)]->id,
                'status' => ['Pending', 'Approved', 'Rejected'][$index % 3],
                'cover_letter' => 'I am interested in this position and believe I am a great fit.',
                'phone' => '09876543' . str_pad($index, 3, '0', STR_PAD_LEFT),
                'resume_link' => 'https://example.com/resumes/user' . $user->id . '.pdf',
            ]);
        }
    }
}
