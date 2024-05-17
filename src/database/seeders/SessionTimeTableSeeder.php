<?php

namespace Database\Seeders;

use App\Models\SessionTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SessionTimeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        SessionTime::truncate();
        foreach (range(1, 2) as $num) {
            SessionTime::create([
                'id' => $num,
                'session_time' => '11:00~13:00',
            ]);
        }
    }
}
