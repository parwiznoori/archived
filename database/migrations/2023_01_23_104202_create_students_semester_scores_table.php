<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsSemesterScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students_semester_scores', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('student_id')->unsigned();
            $table->foreign('student_id')
            ->references('id')
            ->on('students')
            ->onDelete('cascade');

            $table->integer('subject_id')->unsigned();
            $table->foreign('subject_id')
            ->references('id')
            ->on('subjects')
            ->onDelete('cascade');

            $table->integer('course_id')->unsigned()->nullable();
            $table->foreign('course_id')
            ->references('id')
            ->on('courses')
            ->onDelete('cascade'); 

            $table->integer('score_id')->unsigned()->nullable();
            $table->foreign('score_id')
            ->references('id')
            ->on('scores')
            ->onDelete('cascade');

            $table->smallInteger('education_year')->nullable();
            $table->tinyInteger('semester');

            $table->float('chance_one', 5,2)->nullable();
            $table->float('chance_two', 5,2)->nullable();
            $table->float('chance_three', 5,2)->nullable();
            $table->float('chance_four', 5,2)->nullable();

            $table->float('success_score', 5,2);
            $table->enum('success_chance', [1,2,3,4])->default(1);

            
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['student_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students_semester_scores');
    }
}
