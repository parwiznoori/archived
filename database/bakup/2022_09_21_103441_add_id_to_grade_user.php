<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdToGradeUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grade_user', function (Blueprint $table) {
            //
            $table->increments('id')->first();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('grade_id')->references('id')->on('grades');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grade_user', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropForeign(['user_id']);
            $table->dropForeign(['grade_id']);
        });
    }
}
