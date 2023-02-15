<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        if (config('app.env') === 'production') {
            // 本番環境
            $this->call([
                AdminSeeder::class,
            ]);
        } else {
            $this->call([
                AdminSeeder::class,
                UserSeeder::class,
                GroupSeeder::class,
                TodoSeeder::class,
                GroupUserSeeder::class
            ]);
        }
    }
}
