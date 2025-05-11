<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursePoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_policies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->integer('course_id');
            $table->integer('teacher_id');
            $table->text('scoring_and_evaluation_methods')->nullable();
            $table->text('educational_and_learning_goals')->nullable();
            $table->text('teaching_methods')->nullable();
            // $table->text('study_material')->nullable();
            $table->text('class_rules')->nullable();
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
        Schema::dropIfExists('course_policies');
    }
}
