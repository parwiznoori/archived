<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsStudentsHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups_students_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('student_id')->unsigned(); 
            $table->foreign('student_id')
            ->references('id')
            ->on('students')
            ->onDelete('cascade'); 

            $table->integer('group_id')->unsigned(); 
            $table->foreign('group_id')
            ->references('id')
            ->on('groups')
            ->onDelete('cascade'); 
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups_students_history');
    }
}
