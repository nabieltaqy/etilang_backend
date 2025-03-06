<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the users to be inserted into the database
        $users = [
            [
                'name' => 'Test User',
                'nip' => '1234567890',
                'email' => 'test@example.com',
                'password' => Hash::make('12345678'),
            ],
            [
                'name' => 'Test Admin',
                'nip' => '1234567891',
                'email' => 'admin@example.com',
                'password' => Hash::make('12345678'),
            ],
        ];

        // Insert the users into the database
        foreach ($users as $user) {
           User::create($user);
        }
    }
}
