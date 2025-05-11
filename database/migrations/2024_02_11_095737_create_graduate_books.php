<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGraduateBooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('graduate_books_pdf', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('university_id')->unsigned()->index(); 
            $table->foreign('university_id')
            ->references('id')
            ->on('universities')
            ->onDelete('cascade'); 

            $table->integer('department_id')->unsigned()->index(); 
            $table->foreign('department_id')
            ->references('id')
            ->on('departments')
            ->onDelete('cascade'); 

            $table->integer('grade_id')->unsigned()->index(); 
            $table->foreign('grade_id')
            ->references('id')
            ->on('grades')
            ->onDelete('cascade');
           
            $table->integer('graduated_year')->unsigned()->index();

            $table->string('status', 30);
            $table->string('fileName', 256)->nullable();
            $table->string('viewPath', 255)->nullable();
            $table->string('statusMessage', 512)->nullable();
            
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade'); 

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
        Schema::dropIfExists('graduate_books_pdf');
    }
}
