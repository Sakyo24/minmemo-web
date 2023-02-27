<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // ユーザーのTODO
        $users = User::all();

        foreach ($users as $user) {
            for ($i = 1; $i <= 10; $i++) {
                $date = new Carbon('2021-12-31');
                $add_day = $date->addDay($i);
                Todo::factory()->create([
                    'title' => '個人todo' . $user->id . '-' . $i,
                    'detail' => '個人todo' . $user->id . '-' . $i . 'の詳細です。',
                    'user_id' => $user->id,
                    'group_id' => null,
                    'created_at' => $add_day,
                    'updated_at' => $add_day,
                    'deleted_at' => null,
                ]);
            }
        }

        // グループのTODO
        $groups = Group::all();

        foreach ($groups as $key => $group) {
            $group_num = $key + 1;

            for ($i = 1; $i <= 10; $i++) {
                $date = new Carbon('2021-12-31');
                $add_day = $date->addDay($i);
                Todo::factory()->create([
                    'title' => 'グループtodo' . $group_num . '-' . $i,
                    'detail' => 'グループtodo' . $group_num . '-' . $i . 'の詳細です。',
                    'user_id' => $group_num,
                    'group_id' => $group->id,
                    'deleted_at' => null,
                    'created_at' => $add_day,
                    'updated_at' => $add_day,
                ]);
            }
        }
    }
}
