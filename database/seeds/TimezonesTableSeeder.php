<?php

use Illuminate\Database\Seeder;

class TimezonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('timezones')->insert([

            [ 'name_id' => 'Asia/Seoul', 'utc_offset_string' => 'UTC+09:00', 'utc_offset_number' => 9 ],
            [ 'name_id' => 'Asia/Shanghai', 'utc_offset_string' => 'UTC+08:00', 'utc_offset_number' => 8 ],
            [ 'name_id' => 'Asia/Taipei', 'utc_offset_string' => 'UTC+08:00', 'utc_offset_number' => 8 ],
            [ 'name_id' => 'Asia/Tokyo', 'utc_offset_string' => 'UTC+09:00', 'utc_offset_number' => 9 ],
            [ 'name_id' => 'Asia/Bangkok', 'utc_offset_string' => 'UTC+07:00', 'utc_offset_number' => 7 ],
        ]);
    }
}
