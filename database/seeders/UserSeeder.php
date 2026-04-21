<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's users table with demo accounts.
     */
    public function run(): void
    {
        $demoUsers = [
            [
                'name' => 'Review Admin',
                'email' => 'review.admin@example.com',
            ],
            [
                'name' => 'Review Member',
                'email' => 'review.member@example.com',
            ],
        ];

        foreach ($demoUsers as $demoUser) {
            User::query()->updateOrCreate(
                ['email' => $demoUser['email']],
                [
                    'name' => $demoUser['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ],
            );
        }

        User::factory()
            ->count(8)
            ->create();
    }
}
