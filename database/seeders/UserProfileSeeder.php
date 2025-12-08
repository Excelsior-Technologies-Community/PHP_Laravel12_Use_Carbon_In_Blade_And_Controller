<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserProfile;
use Carbon\Carbon;

class UserProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'birth_date' => Carbon::parse('1990-05-15'),
                'subscription_expiry' => Carbon::now()->addDays(15),
                'created_at' => Carbon::now()->subMonths(2),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'birth_date' => Carbon::parse('1985-12-10'),
                'subscription_expiry' => Carbon::now()->addMonths(3),
                'created_at' => Carbon::now()->subDays(45),
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
                'birth_date' => Carbon::parse('1978-08-22'),
                'subscription_expiry' => Carbon::now()->subDays(10), // Expired
                'created_at' => Carbon::now()->subYear(),
            ],
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'birth_date' => Carbon::parse('1995-03-30'),
                'subscription_expiry' => Carbon::now()->addYear(),
                'created_at' => Carbon::now()->subWeeks(2),
            ],
        ];

        foreach ($profiles as $profile) {
            UserProfile::create($profile);
        }
    }
}