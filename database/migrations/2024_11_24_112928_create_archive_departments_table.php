<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive_departments', function (Blueprint $table) {
            $table->bigIncrements('id'); // Primary key

            $table->bigInteger('archive_id')->unsigned()->index();
            $table->foreign('archive_id')
                ->references('id')
                ->on('archives')
                ->onDelete('cascade');

            $table->integer('university_id')->unsigned()->index();
            $table->foreign('university_id')
                ->references('id')
                ->on('universities')
                ->onDelete('cascade');

            $table->integer('faculty_id')->unsigned()->index();
            $table->foreign('faculty_id')
                ->references('id')
                ->on('faculties')
                ->onDelete('cascade');

            $table->integer('department_id')->unsigned()->index();
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');



        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archive_departments');
    }
}
