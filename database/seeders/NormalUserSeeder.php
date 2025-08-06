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

        $user = User::where('email', 'user@example.com')->first();
        $user2 = User::where('email', 'daniel@example.com')->first();

        if (!$user) {
            $user = User::create([
                'id' => (string)Str::uuid(),
                'name' => 'Usuário Normal',
                'email' => 'user@example.com',
                'password' => Hash::make('user1234'),
            ]);
        }

        if (!$user2) {
            $user2 = User::create([
                'id' => (string)Str::uuid(),
                'name' => 'daniel Normal',
                'email' => 'daniel@example.com',
                'password' => Hash::make('user1234'),
            ]);
        }

        if (!$user->hasRole('user')) {
            $user->assignRole($role);
        }

        if (!$user2->hasRole('user')) {
            $user2->assignRole($role);
        }

        $token = JWTAuth::fromUser($user);

        $this->command->info("User created or exists: email={$user->email}, password=user1234");
        $this->command->info("JWT Token (use in Authorization header):");
        $this->command->line($token);


        $token2 = JWTAuth::fromUser($user2);

        $this->command->info("User created or exists: email={$user2->email}, password=user1234");
        $this->command->info("JWT Token (use in Authorization header):");
        $this->command->line($token2);
    }
}
