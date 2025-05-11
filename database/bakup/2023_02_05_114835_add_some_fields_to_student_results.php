<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeFieldsToStudentResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_results', function (Blueprint $table) {
            $table->smallInteger('education_year')->after('department_id')->nullable();
            $table->boolean('increase_semester')->after('isPassed')->default(0);
            $table->double('semester_credits',5,2)->after('increase_semester')->nullable();
            $table->double('success_credits',5,2)->after('semester_credits')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_results', function (Blueprint $table) {
            $table->dropColumn(['education_year','increase_semester','semester_credits','success_credits']);
        });
    }
}
