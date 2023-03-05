<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $date = new Carbon('2021-12-31');
            $add_day = $date->addDay($i);
            User::factory()->create(
                [
                    'name' => 'ユーザー' . $i,
                    'email' => 'user' . $i . '@test.ne.jp',
                    'email_verified_at' => null,
                    'password' => Hash::make('user' . $i),
                    'remember_token' => null,
                    'created_at' => $add_day,
                    'updated_at' => $add_day,
                    'deleted_at' => null,
                ]
            );
        }
    }
}
