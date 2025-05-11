<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemesterDeprivedStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('semester_deprived_students', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('university_id')->unsigned()->index(); 
            $table->foreign('university_id')
            ->references('id')
            ->on('universities')
            ->onDelete('cascade'); 

            $table->integer('department_id')->unsigned()->index(); 
            $table->foreign('department_id')
            ->references('id')
            ->on('departments')
            ->onDelete('cascade'); 

            $table->integer('student_id')->unsigned()->index(); 
            $table->foreign('student_id')
            ->references('id')
            ->on('students')
            ->onDelete('cascade');
           
            $table->integer('educational_year')->index();
            $table->tinyInteger('semester');
            $table->enum('half_year', ['spring', 'fall', 'winter'])->default('spring')->index();

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
        Schema::dropIfExists('semester_deprived_students');
    }
}
