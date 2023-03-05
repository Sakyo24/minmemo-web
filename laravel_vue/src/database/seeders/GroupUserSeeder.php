<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupUser;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class GroupUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $groups = Group::all();

        foreach ($groups as $key => $group) {
            $group_num = $key + 1;
            $date = new Carbon('2021-12-31');
            $add_day = $date->addDay($group_num);
            GroupUser::factory()->create([
                'user_id' => $group_num,
                'group_id' => $group->id,
                'created_at' => $add_day,
                'updated_at' => $add_day,
                'deleted_at' => null,
            ]);
            GroupUser::factory()->create([
                'user_id' => $group_num === 10 ? 1 : $group_num + 1,
                'group_id' => $group->id,
                'created_at' => $add_day,
                'updated_at' => $add_day,
                'deleted_at' => null,
            ]);
        }
    }
}
