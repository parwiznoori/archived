<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUniversityUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('university_users', function (Blueprint $table) {
             $table->bigIncrements('id');
            $table->integer('university_id')->unsigned()->nullable()->index(); 
            
            $table->foreign('university_id')
            ->references('id')
            ->on('universities')
            ->onDelete('cascade'); 
            

            $table->integer('user_id')->unsigned()->index(); 
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade'); 

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
        Schema::dropIfExists('university_users');
    }
}
