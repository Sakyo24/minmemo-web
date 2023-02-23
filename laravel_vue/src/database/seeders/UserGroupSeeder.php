<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Group;
use Carbon\Carbon;

class GroupUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [];
        $groups = Group::all();
        foreach ($groups as $key => $group) {
            $group_num = $key + 1;
            $dt = new Carbon('2021-12-31');
            $this_day = $dt->addDay($group_num);
            $datas[] = [
                'user_id'    => $group_num,
                'group_id'   => $group->id,
                'deleted_at' => null,
                'created_at' => $this_day,
                'updated_at' => $this_day,
            ];
            $datas[] = [
                'user_id'    => $group_num == 10 ? 1 : $group_num + 1,
                'group_id'   => $group->id,
                'deleted_at' => null,
                'created_at' => $this_day,
                'updated_at' => $this_day,
            ];
        }

        DB::table('group_user')->insert($datas);
    }
}
