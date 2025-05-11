<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkToCourseStudent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('course_student', function (Blueprint $table) {

            $table->integer('student_id')->unsigned()->index()->change(); 
            $table->foreign('student_id')
            ->references('id')
            ->on('students')
            ->onDelete('restrict'); 


            $table->integer('course_id')->unsigned()->index()->change(); 
            $table->foreign('course_id')
            ->references('id')
            ->on('courses')
            ->onDelete('restrict'); 
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('course_student', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['course_id']);
           
            $table->dropForeign(['student_id']);
            $table->dropForeign(['course_id']);
        });
        Schema::enableForeignKeyConstraints();
    }
}
