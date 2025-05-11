<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetForeginKeyToLeaves extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('leaves', function (Blueprint $table) {

            $table->integer('student_id')->unsigned()->index()->change(); 
            $table->foreign('student_id')
            ->references('id')
            ->on('students')
            ->onDelete('cascade'); 

            $table->integer('university_id')->unsigned()->index()->change(); 
            $table->foreign('university_id')
            ->references('id')
            ->on('universities')
            ->onDelete('cascade'); 

            $table->integer('department_id')->unsigned()->nullable()->after('university_id');
            $table->foreign('department_id')
            ->references('id')
            ->on('departments')
            ->onDelete('cascade'); 

        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropForeign(['student_id']);
            $table->dropIndex(['university_id']);
            $table->dropForeign(['university_id']);
            $table->dropForeign(['department_id']);
        });
        Schema::enableForeignKeyConstraints();
    }
}
