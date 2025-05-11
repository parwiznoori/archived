<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeValidationGorups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('kankor_year')->nullable(false)->change();

            $table->integer('university_id')->unsigned()->index()->change(); 
            // $table->foreign('university_id')
            // ->references('id')
            // ->on('universities')
            // ->onDelete('cascade'); 

            $table->integer('department_id')->unsigned()->index()->change(); 
            // $table->foreign('department_id')
            // ->references('id')
            // ->on('departments')
            // ->onDelete('cascade'); 
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('kankor_year')->nullable()->change();
            $table->dropIndex(['university_id']);
            $table->dropIndex(['department_id']);
        });
    }
}
