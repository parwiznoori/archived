<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldCourseStatusIdToCourses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            
            $table->integer('course_status_id')->unsigned()->after('active')->nullable();
            $table->foreign('course_status_id')
            ->references('id')
            ->on('course_status')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['course_status_id']);
            $table->dropForeign(['course_status_id']);
        });
    }
}
