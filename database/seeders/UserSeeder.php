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
                'name'     => 'Alice Johnson',
                'email'    => 'alice@test.com',
            ],
            [
                'name'     => 'Bob Smith',
                'email'    => 'bob@test.com',
            ],
            [
                'name'     => 'Charlie Davis',
                'email'    => 'charlie@test.com',
            ],
            [
                'name'     => 'Diana Lee',
                'email'    => 'diana@test.com',
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
    }
}
