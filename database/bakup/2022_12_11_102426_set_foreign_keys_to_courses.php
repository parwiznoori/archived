<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetForeignKeysToCourses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('courses', function (Blueprint $table) {
           
            $table->integer('university_id')->unsigned()->index()->change(); 
            $table->foreign('university_id')
            ->references('id')
            ->on('universities')
            ->onDelete('cascade'); 

            $table->integer('subject_id')->unsigned()->index()->change(); 
            $table->foreign('subject_id')
            ->references('id')
            ->on('subjects')
            ->onDelete('cascade'); 

            $table->integer('teacher_id')->unsigned()->index()->change(); 
            $table->foreign('teacher_id')
            ->references('id')
            ->on('teachers')
            ->onDelete('cascade'); 

            $table->integer('department_id')->unsigned()->index()->change(); 
            $table->foreign('department_id')
            ->references('id')
            ->on('departments')
            ->onDelete('cascade'); 
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
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['university_id']);
            $table->dropIndex(['department_id']);
            $table->dropIndex(['teacher_id']);
            $table->dropIndex(['subject_id']);
            $table->dropForeign(['university_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['teacher_id']);
            $table->dropForeign(['subject_id']);
        });
        Schema::enableForeignKeyConstraints();
    }
}
