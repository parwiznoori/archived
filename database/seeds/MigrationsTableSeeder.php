<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('migrations')->insert([
            'migration' => '2022_12_17_114810_set_fk_to_students_table',
            'batch' => 44,
        ]);
    }
}
