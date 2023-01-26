<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Admin::factory()->create([
            'name' => env('INIT_ADMIN_NAME'),
            'email' => env('INIT_ADMIN_EMAIL'),
            'email_verified_at' => now(),
            'password' => Hash::make(env('INIT_ADMIN_PASSWORD')),
        ]);
    }
}
