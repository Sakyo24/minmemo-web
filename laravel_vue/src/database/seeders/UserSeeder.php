<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSeeder extends Seeder
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
                'name'       => 'ユーザー' . $i,
                'email'      => 'user' . $i . '@test.ne.jp',
                'password'   => bcrypt('user' . $i),
                'created_at' => $this_day,
                'updated_at' => $this_day,
            ];
        }

        DB::table('users')->insert($datas);
    }
}
