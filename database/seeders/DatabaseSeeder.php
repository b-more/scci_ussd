<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ApiLogSeeder::class);
        $this->call(SmsLogSeeder::class);
        $this->call(VoucherValidationSeeder::class);
        $this->call(VoucherSeeder::class);
        $this->call(UserSeeder::class);
    }
}
