<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin']);

        $user = User::where('email', 'admin@example.com')->first();

        if (!$user) {
            $user = User::create([
                'id' => (string)Str::uuid(),
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin1234'),
            ]);
        }

        if (!$user->hasRole('admin')) {
            $user->assignRole($role);
        }

        $token = JWTAuth::fromUser($user);

        $this->command->info("Admin user created or exists: email={$user->email}, password=admin1234");
        $this->command->info("JWT Token (use in Authorization header):");
        $this->command->line($token);
    }
}
