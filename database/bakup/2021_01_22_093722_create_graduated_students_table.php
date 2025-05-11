<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('form_no')->nullable();
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('grandfather_name')->nullable();
            $table->integer('university_id');
            $table->integer('department_id');
            $table->string('kankor_year')->nullable();
            $table->string('kankor_result')->nullable();
            $table->string('birthdate')->nullable();
            $table->string('graduation_year')->nullable();
            $table->boolean('is_monograph_completed')->default(false);
            $table->string('phone')->nullable();
            $table->string('tazkira')->nullable();
            $table->integer('enrolment_type')->nullable();
            $table->integer('grade_id');
            $table->float('kankor_score')->nullable();
            $table->string('language')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->string('school_name')->nullable();
            $table->integer('province')->nullable();
            $table->string('district')->nullable();
            $table->string('village')->nullable();
            $table->string('address')->nullable();
            $table->softDeletes();
            $table->timestamps();
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
