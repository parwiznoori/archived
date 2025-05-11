<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGraduatedStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('graduated_students', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('university_id'); 
            $table->foreign('university_id')
            ->references('id')
            ->on('universities')
            ->onDelete('cascade'); 

            $table->unsignedInteger('department_id');
            $table->foreign('department_id')
            ->references('id')
            ->on('departments')
            ->onDelete('cascade'); 

            $table->unsignedInteger('student_id');
            $table->foreign('student_id')
            ->references('id')
            ->on('students')
            ->onDelete('cascade'); 

            
            $table->unsignedInteger('grade_id');
            $table->foreign('grade_id')
            ->references('id')
            ->on('grades')
            ->onDelete('cascade'); 

            $table->integer('graduated_year');
            $table->boolean('manual_graduated')->default(0);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('graduated_students');
    }
}
