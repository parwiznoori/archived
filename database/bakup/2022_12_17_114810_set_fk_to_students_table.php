<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetFkToStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('students', function (Blueprint $table) {

            $table->integer('university_id')->unsigned()->index()->change(); 
            $table->foreign('university_id')
            ->references('id')
            ->on('universities')
            ->onDelete('cascade'); 

            $table->integer('department_id')->unsigned()->change(); 
            $table->foreign('department_id')
            ->references('id')
            ->on('departments')
            ->onDelete('cascade'); 

            $table->integer('status_id')->unsigned()->index()->change(); 
            $table->foreign('status_id')
            ->references('id')
            ->on('student_statuses')
            ->onDelete('cascade'); 

            $table->integer('group_id')->unsigned()->index()->change(); 
            $table->foreign('group_id')
            ->references('id')
            ->on('groups')
            ->onDelete('cascade'); 

            $table->integer('grade_id')->unsigned()->index()->change(); 
            $table->foreign('grade_id')
            ->references('id')
            ->on('grades')
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
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['university_id']);
            // $table->dropIndex(['department_id']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['group_id']);
            $table->dropIndex(['grade_id']);
            $table->dropForeign(['university_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['status_id']);
            $table->dropForeign(['group_id']);
            $table->dropForeign(['grade_id']);
        });
        Schema::enableForeignKeyConstraints();
    }
}
