<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HolidaysSeeder extends Seeder
{
    public function run(): void
    {
        $year = now()->year; // automatically use current year

        $holidays = [
            // 🌞 Regular Holidays
            ['New Year\'s Day', 'Regular', "$year-01-01"],
            ['Araw ng Kagitingan', 'Regular', "$year-04-09"],
            ['Maundy Thursday', 'Regular', "$year-04-17"], // sample; adjust each year if movable
            ['Good Friday', 'Regular', "$year-04-18"],     // sample; adjust each year if movable
            ['Labor Day', 'Regular', "$year-05-01"],
            ['Independence Day', 'Regular', "$year-06-12"],
            ['National Heroes Day', 'Regular', "$year-08-25"],
            ['Bonifacio Day', 'Regular', "$year-11-30"],
            ['Christmas Day', 'Regular', "$year-12-25"],
            ['Rizal Day', 'Regular', "$year-12-30"],

            // 🌙 Special Non-Working Holidays
            ['Ninoy Aquino Day', 'Special', "$year-08-21"],
            ['All Saints\' Day', 'Special', "$year-11-01"],
            ['All Souls\' Day', 'Special', "$year-11-02"],
            ['Christmas Eve', 'Special', "$year-12-24"],
            ['New Year\'s Eve', 'Special', "$year-12-31"],
        ];

        // ✅ Replace your old insert loop with this updateOrInsert loop
        foreach ($holidays as $holiday) {
            DB::table('holidays')->updateOrInsert(
                [
                    'holiday_name' => $holiday[0],
                    'holiday_date' => $holiday[2],
                ],
                [
                    'holiday_type' => $holiday[1],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
