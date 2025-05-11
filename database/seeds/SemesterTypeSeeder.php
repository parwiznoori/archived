<?php

use Illuminate\Database\Seeder;

class SemesterTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('semester_type')->insert([
            [
                'id' => 1,
                'name' => 'بهاری',
                'name_en' => 'spring',
            ],
            [
                'id' => 2,
                'name' => 'خزانی',
                'name_en' => 'fall',
            ]
        ]);
    }
}
