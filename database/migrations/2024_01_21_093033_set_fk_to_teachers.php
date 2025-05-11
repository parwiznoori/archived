<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetFkToTeachers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('teachers', function (Blueprint $table) {

            $table->integer('university_id')->unsigned()->index()->change(); 
            $table->foreign('university_id')
            ->references('id')
            ->on('universities')
            ->onDelete('cascade'); 

            $table->integer('department_id')->unsigned()->index()->change(); 
            $table->foreign('department_id')
            ->references('id')
            ->on('departments')
            ->onDelete('cascade'); 

            $table->integer('academic_rank_id')->unsigned()->index()->change(); 
            $table->foreign('academic_rank_id')
            ->references('id')
            ->on('teacher_academic_ranks')
            ->onDelete('cascade'); 

            $table->integer('province')->unsigned()->index()->change(); 
            $table->foreign('province')
            ->references('id')
            ->on('provinces')
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
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropIndex(['university_id']);
            $table->dropIndex(['department_id']);
            $table->dropIndex(['academic_rank_id']);
            $table->dropIndex(['province']);
            $table->dropForeign(['university_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['academic_rank_id']);
            $table->dropForeign(['province']);
        });
        Schema::enableForeignKeyConstraints();
    }
}
