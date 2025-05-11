<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetFkToGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('groups', function (Blueprint $table) {

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
        Schema::table('groups', function (Blueprint $table) {
            // 
            $table->dropForeign(['university_id']);
            $table->dropForeign(['department_id']);
        });
        Schema::enableForeignKeyConstraints();
    }
}
