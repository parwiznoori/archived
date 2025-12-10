<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueStudentToArchivedatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::table('archivedatas', function (Blueprint $table) {
            $table->unique(
                ['name', 'father_name', 'grandfather_name', 'university_id', 'faculty_id', 'department_id'],
                'unique_student'
            );
        });
    }

    public function down()
    {
        Schema::table('archivedatas', function (Blueprint $table) {
            $table->dropUnique('unique_student');
        });
    }
}
