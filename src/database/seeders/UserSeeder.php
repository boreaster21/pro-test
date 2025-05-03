<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);


        User::factory()->create([
            'name' => 'General User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'general',
        ]);

        User::factory(10)->create([
            'role' => 'general'
        ]);
    }
}
