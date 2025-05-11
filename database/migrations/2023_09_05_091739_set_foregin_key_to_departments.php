<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetForeginKeyToDepartments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('departments', function (Blueprint $table) {

            $table->integer('university_id')->unsigned()->index()->change(); 
            $table->foreign('university_id')
            ->references('id')
            ->on('universities')
            ->onDelete('cascade'); 

            $table->integer('grade_id')->unsigned()->index()->change(); 
            $table->foreign('grade_id')
            ->references('id')
            ->on('grades')
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
        Schema::table('departments', function (Blueprint $table) {
            $table->dropIndex(['university_id']);
            $table->dropIndex(['grade_id']);
            $table->dropForeign(['university_id']);
            $table->dropForeign(['grade_id']);
        });
        Schema::enableForeignKeyConstraints();
    }
}
