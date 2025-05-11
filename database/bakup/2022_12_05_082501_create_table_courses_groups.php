<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCoursesGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_group', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned(); 
            $table->foreign('course_id')
            ->references('id')
            ->on('courses')
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
        Schema::dropIfExists('course_group');
    }
}
