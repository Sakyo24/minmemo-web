<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Group;
use Carbon\Carbon;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [];

        $users = User::all();
        foreach ($users as $user) {
            for ($i = 1; $i <= 10; $i++) {
                $dt = new Carbon('2021-12-31');
                $this_day = $dt->addDay($i);
                $datas[] = [
                    'title'      => '個人todo' . $user->id . '-' . $i,
                    'detail'     => '個人todo' . $user->id . '-' . $i . 'の詳細です。',
                    'user_id'    => $user->id,
                    'group_id'   => null,
                    'deleted_at' => null,
                    'created_at' => $this_day,
                    'updated_at' => $this_day,
                ];
            }
        }

        $groups = Group::all();
        foreach ($groups as $key => $group) {
            $group_num = $key + 1;
            for ($i = 1; $i <= 10; $i++) {
                $dt = new Carbon('2021-12-31');
                $this_day = $dt->addDay($i);
                $datas[] = [
                    'title'      => 'グループtodo' . $group_num . '-' . $i,
                    'detail'     => 'グループtodo' . $group_num . '-' . $i . 'の詳細です。',
                    'user_id'    => $group_num,
                    'group_id'   => $group->id,
                    'deleted_at' => null,
                    'created_at' => $this_day,
                    'updated_at' => $this_day,
                ];
            }
        }

        DB::table('todos')->insert($datas);
    }
}
