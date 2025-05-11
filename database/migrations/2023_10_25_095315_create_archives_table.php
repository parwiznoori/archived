|<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->bigIncrements('id');
         
            $table->string('book_name');
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


            $table->date('book_year');
            $table->integer('book_pagenumber');
            $table->string('book_description');
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
        Schema::dropIfExists('archives');
    }
}
