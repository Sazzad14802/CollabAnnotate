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
                'username' => 'alice_j',
                'email'    => 'alice@test.com',
            ],
            [
                'name'     => 'Bob Smith',
                'username' => 'bob_s',
                'email'    => 'bob@test.com',
            ],
            [
                'name'     => 'Charlie Davis',
                'username' => 'charlie_d',
                'email'    => 'charlie@test.com',
            ],
            [
                'name'     => 'Diana Lee',
                'username' => 'diana_l',
                'email'    => 'diana@test.com',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name'     => $userData['name'],
                    'username' => $userData['username'],
                    'password' => Hash::make('123'),
                ]
            );
        }
    }
}
