<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetFkToScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('scores', function (Blueprint $table) {

            $table->integer('course_id')->unsigned()->index()->change(); 
            $table->foreign('course_id')
            ->references('id')
            ->on('courses')
            ->onDelete('cascade'); 

            $table->integer('student_id')->unsigned()->change(); 
            $table->foreign('student_id')
            ->references('id')
            ->on('students')
            ->onDelete('cascade'); 

            $table->integer('subject_id')->unsigned()->index()->change(); 
            $table->foreign('subject_id')
            ->references('id')
            ->on('subjects')
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
        Schema::table('scores', function (Blueprint $table) {
            $table->dropIndex(['course_id']);
            $table->dropIndex(['student_id']);
            $table->dropIndex(['subject_id']);
           
            $table->dropForeign(['course_id']);
            $table->dropForeign(['student_id']);
            $table->dropForeign(['subject_id']);
            
        });
        Schema::enableForeignKeyConstraints();
    }
    
}
