<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaculties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('university_id')->unsigned();
            $table->foreign('university_id')->references('id')->on('universities')->onDelete('cascade');
            $table->string('name',255);
            $table->string('name_en',255)->nullable(); 
            $table->string('chairman',100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['university_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faculties');
    }
}
