<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class TestTableSeeder extends Seeder
{
    public function run()
    {
        $f = Factory::create();
        $data = [];
        for ($i = 0; $i <= 50; $i++) {
            $data[] = [
                'test_number' => rand(0, 50),
                'test_boolean' => rand(0, 1),
                'test_string' => $f->word,
                'test_enum' => rand(0, 1) ? 'value1' : 'value2',
                'test_date' => $f->date($format = 'Y-m-d', $max = 'now'),
                'test_time' => $f->time($format = 'H:i:s', $max = 'now'),
                'test_date_time' => $f->dateTime($max = 'now', $timezone = null),
                'test_images' => $f->imageUrl($width = 320, $height = 240),
                'test_description' => $f->text($maxNbChars = 300)
            ];

        }
        DB::table('tests')->insert(
            $data
        );
    }
}
