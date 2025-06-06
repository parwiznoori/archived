<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGenderToGorups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->enum('gender', ['m', 'f', 'b'])->default('b')->comment('m for male, f for female, b for male and female (both)')->after('university_id');
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
            $table->dropColumn(['gender']);
        });
    }
}
