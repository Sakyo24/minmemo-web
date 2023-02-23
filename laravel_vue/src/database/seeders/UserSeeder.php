<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
                'name' => 'ユーザー' . $i,
                'email' => 'user' . $i . '@test.ne.jp',
                'email_verified_at' => null,
                'password' => Hash::make('user' . $i),
                'remember_token' => null,
                'created_at' => $this_day,
                'updated_at' => $this_day,
                'deleted_at' => null,
            ];
        }

        DB::table('users')->insert($datas);
    }
}
