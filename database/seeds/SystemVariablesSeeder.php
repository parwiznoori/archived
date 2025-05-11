<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SystemVariablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // Entity::create(['name' => 'booklets' ,'description' => 'booklets' ]);
        DB::table('system_variables')->insert([
            'name' => 'NUMBWR_OF_SESSIONS_PER_SEMESTER',
            'default_value' => 16,
            'user_value' => 14,
            'description' => 'تعداد جلساتی که در هر سمستر برگزار میشود توسط این فیلد صورت میگیرد. مفدار پیش فرض 16 جلسه است.در بعضی موارد مانند مساله کرونا و یا عوامل دیگر ممکن است کم یا زیاد شود. در این صورت مقدار فیلد یوزر باید تغییر داده شود. از فیلد نام در سورس کد استفاده شده است فلذا از تغییر نام خوداداری کنید.',
        ]);
    }
}
