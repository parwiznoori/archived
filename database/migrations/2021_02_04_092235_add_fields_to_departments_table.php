<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('departments', function (Blueprint $table) {
            
            $table->string('department_eng')->nullable();
            $table->string('faculty_eng')->nullable();
        });
        \DB::table('departments')->update(['faculty_eng' => 'NA','department_eng' => 'NA']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('department_eng');
            $table->dropColumn('faculty_eng');
        });
    }
}
