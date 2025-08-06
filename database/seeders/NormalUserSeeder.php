<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

class NormalUserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'user']);

        $usersData = [
            [
                'name' => 'Usuário Normal 1',
                'email' => 'user1@example.com',
                'password' => 'user1234',
            ],
            [
                'name' => 'Usuário Normal 2',
                'email' => 'user2@example.com',
                'password' => 'user1234',
            ],
            [
                'name' => 'Usuário Normal 3',
                'email' => 'user3@example.com',
                'password' => 'user1234',
            ],
            [
                'name' => 'Daniel Normal',
                'email' => 'daniel@example.com',
                'password' => 'user1234',
            ],
            [
                'name' => 'Usuário Normal 5',
                'email' => 'user5@example.com',
                'password' => 'user1234',
            ],
        ];

        foreach ($usersData as $userData) {
            $user = User::where('email', $userData['email'])->first();

            if (!$user) {
                $user = User::create([
                    'id' => (string)Str::uuid(),
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                ]);
                $this->command->info("User created: {$user->email}");
            } else {
                $this->command->info("User already exists: {$user->email}");
            }

            if (!$user->hasRole('user')) {
                $user->assignRole($role);
            }

            $token = JWTAuth::fromUser($user);

            $this->command->info("JWT Token for {$user->email}:");
            $this->command->line($token);
        }
    }
}
