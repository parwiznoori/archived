<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentGradeAndFieldOfStudyAndAverageScoreToTableStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('field_of_study', 100)->nullable();
            $table->double('average_score', 5, 2)->nullable();
            $table->integer('current_grade_id')->unsigned()->nullable()->default(1);
            $table->foreign('current_grade_id')
            ->references('id')
            ->on('current_grade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            // $table->dropIndex(['current_grade_id']);
            $table->dropForeign(['current_grade_id']);
            $table->dropColumn('field_of_study');
            $table->dropColumn('average_score');
        });
    }
}
