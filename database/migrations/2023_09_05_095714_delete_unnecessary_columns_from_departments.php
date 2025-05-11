<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteUnnecessaryColumnsFromDepartments extends Migration
{
    public function dropColumnIfExists($tbl,$column)
    {
        if (Schema::hasColumn($tbl, $column)) {
            Schema::table($tbl, function (Blueprint $table) use ($column) {
                $table->dropColumn($column);
            });
        }
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tbl = 'departments';
        Schema::table('departments', function (Blueprint $table) use ($tbl) {
            $this->dropColumnIfExists($tbl,'faculty');
            $this->dropColumnIfExists($tbl,'department_type');
            $this->dropColumnIfExists($tbl,'faculty_eng');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('departments', function (Blueprint $table) {
            //
        });
    }
}
