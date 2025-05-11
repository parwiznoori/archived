<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetFkAndIndexInTransfers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('transfers', function (Blueprint $table) {
            
            $table->integer('student_id')->unsigned()->index()->change(); 
            $table->foreign('student_id')
            ->references('id')
            ->on('students')
            ->onDelete('cascade'); 

            $table->integer('from_department_id')->unsigned()->index()->change(); 
            $table->foreign('from_department_id')
            ->references('id')
            ->on('departments')
            ->onDelete('cascade'); 

            $table->integer('to_department_id')->unsigned()->index()->change(); 
            $table->foreign('to_department_id')
            ->references('id')
            ->on('departments')
            ->onDelete('cascade'); 

            $table->integer('education_year')->unsigned()->index()->change();

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
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['from_department_id']);
            $table->dropIndex(['to_department_id']);
            $table->dropIndex(['education_year']);

            $table->dropForeign(['student_id']);
            $table->dropForeign(['from_department_id']);
            $table->dropForeign(['to_department_id']);
        });
        Schema::enableForeignKeyConstraints();
    }
}
