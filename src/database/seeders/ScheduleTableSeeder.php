<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ScheduleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //ここから追加
        $texts = [
            '半日希望',
            '×(出勤不可)',
            '×(出勤不可)',
            // '早田：出勤可能',
        ];

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schedule::truncate();
        foreach ($texts as $text) {

            Schedule::create([
                'uuid' => (string)Str::uuid(),
                'date' => '2023-12-11',
                'text' => $text,
            ]);
        }
        Schedule::create([
            'uuid' => (string)Str::uuid(),
            'date' => '2023-12-12',
            'text' => '×(出勤不可)',
        ]);
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        //ここまで追加
    }
}
