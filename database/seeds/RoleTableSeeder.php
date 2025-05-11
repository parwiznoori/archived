<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'id' => 28,
                'name' => 'system-developer',
                'guard_name' => 'user',
                'created_at' => '2023-12-17 04:47:47',
                'updated_at' => '2023-12-18 04:01:44',
                'deleted_at' => 'NULL',
                'title' => 'System Developer',
                'admin' => '1',
                'type_id' => '1',
                'priority' => '1000'
            ]
        ]);

        DB::table('model_has_roles')->insert([
            [
                'role_id' => 28,
                'model_type' => 'App\\User',
                'model_id' => 1293
            ]
        ]);
    }
}
