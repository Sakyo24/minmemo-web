<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Inquiry;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InquirySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        for ($i = 1; $i <= 100; $i++) {
            $date = new Carbon('2021-12-31');
            $add_day = $date->addDay($i);
            Inquiry::factory()->create(
                [
                    'status' => (($i - 1) % 3) + 1,
                    'user_id' => ($i <= 50) ? ((($i - 1) % 10) + 1) : null,
                    'name' => ($i <= 50) ? null : ("ゲスト" . $i),
                    'email' => ($i <= 50) ? null : ('guest' . $i . '@test.ne.jp'),
                    'title' => "お問い合わせ" . $i,
                    'detail' => "お問い合わせ詳細" . $i . "\nお問い合わせ詳細" . $i . "\nお問い合わせ詳細" . $i,
                    'created_at' => $add_day,
                    'updated_at' => $add_day,
                ]
            );
        }
    }
}
