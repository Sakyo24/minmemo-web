<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GroupSeeder extends Seeder
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
            Group::factory()->create([
                'id' => Str::random(26),
                'name' => 'グループ' . $i,
                'owner_user_id' => $i,
                'deleted_at' => null,
                'created_at' => $add_day,
                'updated_at' => $add_day,
            ]);
        }
    }
}
