<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Admin', 'email' => 'admin@ontech.co.zm'],
            ['name' => 'Blessmore', 'email' => 'blessmore@ontech.co.zm'],
            ['name' => 'Dennis', 'email' => 'dennis@ontech.co.zm'],
            ['name' => 'Moola', 'email' => 'moola@ontech.co.zm'],
            ['name' => 'Kasela', 'email' => 'kasela@ontech.co.zm'],
            ['name' => 'Prisca', 'email' => 'prisca@ontech.co.zm'],
            ['name' => 'Lucy', 'email' => 'lucy@ontech.co.zm'],
            ['name' => 'Edward', 'email' => 'edward@ontech.co.zm'],
            ['name' => 'Isaac', 'email' => 'isaac@ontech.co.zm'],
            ['name' => 'Eric', 'email' => 'eric@ontech.co.zm'],
            ['name' => 'Scci', 'email' => 'scci@ontech.co.zm'],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('Admin.1234'),
            ]);
        }
    }
}
