<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();

        Notification::create([
            'user_id' => $admin->id,
            'title' => 'New Application',
            'message' => 'John Doe applied for Programmer position.',
            'type' => 'Application',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $admin->id,
            'title' => 'Job Posted Successfully',
            'message' => 'Your job posting for Programmer is now live.',
            'type' => 'Success',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $admin->id,
            'title' => 'Application Approved',
            'message' => 'Jane Smith\'s application has been approved.',
            'type' => 'Success',
            'is_read' => true,
        ]);

        Notification::create([
            'user_id' => $admin->id,
            'title' => 'Job Expiring Soon',
            'message' => 'Your Construction Worker job posting expires in 3 days.',
            'type' => 'Warning',
            'is_read' => true,
        ]);
    }
}
