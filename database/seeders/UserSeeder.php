<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Alif',
                'email'    => 'alif@test.com',
            ],
            [
                'name'     => 'Siyam',
                'email'    => 'siyam@test.com',
            ],
            [
                'name'     => 'Abrar',
                'email'    => 'abrar@test.com',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name'     => $userData['name'],
                    'password' => Hash::make('123'),
                ]
            );
        }

        // Admin account
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('admin'),
                'is_admin' => true,
            ]
        );
    }
}
