<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create a demo user for development
        User::firstOrCreate(
            ['email' => 'demo@bima.test'],
            [
                'name'     => 'Demo BIMA',
                'password' => Hash::make('password'),
            ]
        );

        // Seed all agent prompts
        $this->call(AgentPromptSeeder::class);
    }
}
