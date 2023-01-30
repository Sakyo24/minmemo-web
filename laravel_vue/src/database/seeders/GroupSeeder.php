<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [];
        for ($i = 1; $i <= 10; $i++) {
            $dt = new Carbon('2021-12-31');
            $this_day = $dt->addDay($i);
            $datas[] = [
                'id'            => Str::random(26),
                'name'          => 'グループ' . $i,
                'owner_user_id' => $i,
                'deleted_at'    => null,
                'created_at'    => $this_day,
                'updated_at'    => $this_day,
            ];
        }

        DB::table('groups')->insert($datas);
    }
}
