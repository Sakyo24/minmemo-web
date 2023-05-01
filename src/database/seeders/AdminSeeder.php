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
            'name' => config('admin.init.name'),
            'email' => config('admin.init.email'),
            'email_verified_at' => now(),
            'password' => Hash::make(config('admin.init.password')),
        ]);
    }
}
